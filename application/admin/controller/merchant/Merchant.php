<?php

namespace app\admin\controller\merchant;

use app\common\controller\Backend;
use app\common\library\GoogleAuthenticator;
use app\merchant\model\AuthGroupAccess;
use fast\Random;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;
use think\Validate;

/**
 * 商户管理
 *
 * @icon fa fa-circle-o
 */
class Merchant extends Backend
{

    /**
     * Merchant模型对象
     * @var \app\admin\model\merchant\Merchant
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\merchant\Merchant;
        $this->view->assign("statusList", $this->model->getStatusList());
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags', 'trim']);
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                    ->with(['merchantgroup','agent'])
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with(['merchantgroup','agent'])
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            foreach ($list as $row) {
                $row->visible(['id','group_id','agent_id','number','username','nickname','rate','rate_money','add_money','money','block_money','logintime','loginip','jointime','status']);
                $row->visible(['merchantgroup']);
				$row->getRelation('merchantgroup')->visible(['name']);
				$row->visible(['agent']);
				$row->getRelation('agent')->visible(['username']);
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }


    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : $name) : $this->modelValidate;
                        $this->model->validateFailException(true)->validate($validate);
                    }

                    $params['group_id'] = 1;

                    $params['salt'] = Random::alnum();
                    $params['password'] = md5(md5($params['password']) . $params['salt']);
                    $params['pay_password'] = md5($params['pay_password']);
                    $google = new GoogleAuthenticator();
                    $params['google_code'] = $google->createSecret();
                    $params['avatar'] = '/assets/img/avatar.png'; //设置默认头像。
                    $params['joinip'] = request()->ip();
                    $params['jointime'] = time();
                    $params['createtime'] = time();
                    $params['number'] = $this->buildnumber();
                    $params['secret_key'] = md5($params['password'].time());


                    $result = $this->model->allowField(true)->save($params);

                    if ($result === false) {
                        $this->error($this->model->getError());
                    }

                    //过滤不允许的组别,避免越权
                    $dataset = [];
                    $dataset[] = ['uid' => $this->model->id, 'group_id' => 1];
                    $merchantAuthGroupAccess = new AuthGroupAccess();
                    $merchantAuthGroupAccess->saveAll($dataset);

                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were inserted'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }


    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $params = $this->preExcludeFields($params);
                $result = false;
                Db::startTrans();
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : $name) : $this->modelValidate;
                        $row->validateFailException(true)->validate($validate);
                    }

                    if ($params['password']) {
                        if (!Validate::is($params['password'], '\S{6,16}')) {
                            $this->error(__("Please input correct password"));
                        }
                        $params['salt'] = Random::alnum();
                        $params['password'] = md5(md5($params['password']) . $params['salt']);
                    } else {
                        unset($params['password'], $params['salt']);
                    }
                    if ($params['pay_password']) {
                        $params['pay_password'] = md5($params['pay_password']);
                    } else {
                        unset($params['pay_password']);
                    }

                    $result = $row->allowField(true)->save($params);
                    Db::commit();
                } catch (ValidateException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (PDOException $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                } catch (Exception $e) {
                    Db::rollback();
                    $this->error($e->getMessage());
                }
                if ($result !== false) {
                    $this->success();
                } else {
                    $this->error(__('No rows were updated'));
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }


    //重置谷歌密钥
    public function resetGoogleKey($ids = null){

        $row = $this->model->get($ids);
        if (!$row) {
            $this->error('商户不存在');
        }

        $google = new GoogleAuthenticator();
        $google_code = $google->createSecret();

        $data['google_code'] = $google_code;

        $result = $row->save($data);

        if($result !== false){
            $this->success('重置谷歌密钥成功');
        }else{
            $this->error('重置失败');
        }
    }


    //生成商户编号
    public function buildnumber(){
        $num=substr(time(),6);
        $number = 'M'.date('Ymd',time()).$num;

        $re = Db::name('merchant')->where('number',$number)->find();
        if($re){
            return $this->buildnumber();
        }
        return $number;
    }
}
