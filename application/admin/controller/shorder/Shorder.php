<?php

namespace app\admin\controller\shorder;

use app\common\controller\Backend;
use app\common\library\GoogleAuthenticator;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 商户订单管理
 *
 * @icon fa fa-circle-o
 */
class Shorder extends Backend
{

    /**
     * Shorder模型对象
     * @var \app\admin\model\shorder\Shorder
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\shorder\Shorder;
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
                    ->with(['admin'])
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

                $list = $this->model
                    ->with(['admin'])
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            }else{
                $total = $this->model
                    ->with(['admin'])
                    ->where('admin_id',$this->auth->id)
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

                $list = $this->model
                    ->with(['admin'])
                    ->where('admin_id',$this->auth->id)
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();
            }

            foreach ($list as $row) {

                $row->getRelation('admin')->visible(['username']);
            }
            $list = collection($list)->toArray();

            //统计商户余额
            $sh_allmoney = Db::name('applys')->where(['admin_id'=>$this->auth->id,'status'=>1])->sum('amount');
            //统计提交了的单子的金额
            $sh_ordermoney = Db::name('sh_order')->where(['admin_id'=>$this->auth->id])->sum('amount');

            $sh_money  = bcsub($sh_allmoney,$sh_ordermoney,2);


            $result = array("total" => $total, "rows" => $list,"extend" => ['money' => $sh_money]);

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

                    $google = new GoogleAuthenticator();

                    $findadmin = Db::name('admin')->where('id',$this->auth->id)->find();
                    $googleresult = $google->verifyCode($findadmin['google_code'],$params['google_captcha']);
                    if(!$googleresult){
                        $this->error(__('谷歌验证码错误'));
                    }

                    if($findadmin['pay_password'] != md5($params['pay_pwd'])){
                        $this->error(__('支付密码错误'));
                    }
                    //生成订单号
                    $params['out_trade_no'] = $this->buildnumber();


                    //插入到下发订单表里
                    //随机取一个下发用户
                    $users = Db::name('user')->where(['status'=>1,'is_receive'=>1])->select();
                    $users_count = count($users);
                    $userList = $users[mt_rand(0, $users_count - 1)];

                    $orderData['user_id'] = $userList['id'];
                    $orderData['out_trade_no'] = $params['out_trade_no'];//系统订单号
                    $orderData['bank_number'] = $params['bank_number'];//银行账户
                    $orderData['bank_type'] = $params['bank_type'];//开户行
                    $orderData['bank_from'] = $params['bank_from'];//开户支行
                    $orderData['bank_user'] = $params['bank_user'];//姓名
                    $orderData['amount'] = $params['amount'];//金额
                    $orderData['createtime'] = time();
                    $orderData['ordertime'] = time();
                    $orderData['acount'] = $userList['username'];

                    $params['user_id'] = $userList['id'];
                    $params['admin_id'] = $this->auth->id;
                    $result = $this->model->allowField(true)->save($params);

                    Db::name('order')->insert($orderData);

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


    //生成流水号
    public function buildnumber(){
		//19位订单号
        $number = date("YmdHis") . mt_rand(10000,99999);
        $re = Db::name('sh_order')->where('out_trade_no',$number)->find();
        if($re){
            return $this->buildnumber();
        }
        return $number;
    }
}
