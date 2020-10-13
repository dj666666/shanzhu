<?php

namespace app\agent\controller\yhk;

use app\common\controller\AgentBackend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 商户申请充值记录管理
 *
 * @icon fa fa-circle-o
 */
class Apply extends AgentBackend
{

    /**
     * Apply模型对象
     * @var \app\agent\model\yhk\Apply
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\agent\model\yhk\Apply;
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
            if($this->auth->id ==1){
                $total = $this->model
                    ->with(['yhk','admin'])
                    ->where($where)
                    ->order($sort, $order)
                    ->count();
                $list = $this->model
                    ->with(['yhk','admin'])
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            }else{
                $total = $this->model
                    ->with(['yhk','admin'])
                    ->where('admin_id',$this->auth->id)
                    ->where($where)
                    ->order($sort, $order)
                    ->count();
                $list = $this->model
                    ->with(['yhk','admin'])
                    ->where('admin_id',$this->auth->id)
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            }


            foreach ($list as $row) {

                $row->getRelation('yhk')->visible(['bank_user','bank_name','bank_number','bank_type']);
				$row->getRelation('admin')->visible(['username']);
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


                    /*//随机取一个银行卡
                    $count = Db::name('yhk')->where(['is_state'=>1])->select();
                    $countYhk = count($count);

                    if($countYhk<1){
                        $this->error(__('暂无可用银行卡，请联系管理员'));
                    }

                    $list = $count[mt_rand(0, $countYhk - 1)];*/

                    if($params['amount'] < 1){
                    	$this->error(__('充值金额必须大于1'));
                    }

					$yhkId = $params['banknumber'];
                    $params['yhk_id'] = $yhkId;
                    $params['out_trade_no'] = $this->buildnumber();
                    $params['admin_id'] = $this->auth->id;
					unset($params['banknumber']);
                    $result = $this->model->allowField(true)->save($params);

                    //减去银行卡余额
                    $findyhk =Db::name('yhk')->where('id',$yhkId)->field('money')->find();
                    $money = bcsub($findyhk['money'],$params['amount'],2);
                    Db::name('yhk')->where('id',$yhkId)->update(['money'=>$money]);

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

        $yhk = Db::name('yhk')->where('id',$row['yhk_id'])->find();
        $this->view->assign("row", $row);
        $this->view->assign("yhk", $yhk);
        return $this->view->fetch();
    }


    //生成流水号
    public function buildnumber(){

		//15位申请充值订单号
        $number = date("Ymd") . mt_rand(1000000,9999999);
        $re = Db::name('applys')->where('out_trade_no',$number)->find();
        if($re){
            return $this->buildnumber();
        }
        return $number;
    }
}
