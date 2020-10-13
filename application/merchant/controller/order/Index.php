<?php

namespace app\merchant\controller\order;

use app\common\controller\MerchantBackend;
use app\common\library\GoogleAuthenticator;
use think\Config;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 订单管理
 *
 * @icon fa fa-circle-o
 */
class Index extends MerchantBackend
{

    /**
     * Index模型对象
     * @var \app\merchant\model\order\Index
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\merchant\model\order\Index;
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
                    ->with(['user'])
                    ->where('mer_id',$this->auth->id)
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with(['user'])
                    ->where('mer_id',$this->auth->id)
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            foreach ($list as $row) {


            }
            $list = collection($list)->toArray();
            //找出商户余额
            $sh_money = Db::name('merchant')->where(['id'=>$this->auth->id])->value('money');

            $result = array("total" => $total, "rows" => $list, "extend" => ['money' => $sh_money]);
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
                    if($params['amount'] <1){
                        $this->error(__('金额不能低于1元！'));
                    }
                    //找出当前商户
                    $merchant = Db::name('merchant')->where('id',$this->auth->id)->find();

                    if($merchant['status'] == 'hidden'){
                        $this->error(__('账号已被禁用'));
                    }

                    //判断是否开启谷歌验证
                    if (Config::get('fastadmin.order_checkmerchantgoogle')) {
                        $google = new GoogleAuthenticator();
                        $result = $google->verifyCode($merchant['google_code'],$params['google_captcha']);
                        if(!$result){
                            $this->error('谷歌验证码错误');
                        }
                    }

                    if($merchant['pay_password'] != md5($params['pay_pwd'])){
                        $this->error(__('支付密码错误'));
                    }


                    //生成订单号
                    $params['out_trade_no'] = $this->buildnumber();

                    //随机取一个下发用户
                    $users = Db::name('user')->where(['status'=>1,'is_receive'=>1])->select();
                    $users_count = count($users);
                    if($users_count <1){
                        $this->error(__('暂无可用下发人员，请联系管理员'));
                    }
                    $userList = $users[mt_rand(0, $users_count - 1)];

                    $params['user_id'] = $userList['id'];
                    $params['mer_id'] = $this->auth->id;
                    $params['agent_id'] = $this->auth->agent_id;

                    $fees = bcmul($params['amount'],$merchant['rate'],2);//计算出手续费
                    $fees = bcadd($fees,$merchant['add_money'],2);
                    $params['fees'] = $fees;

                    if($merchant['money'] < bcadd($fees,$params['amount'],2)){
                        $this->error(__('余额不足支付此次订单，请先充值'));
                    }

                    $params['createtime'] = time();
                    $params['acount'] = $userList['username'];
                    $params['ip_address'] = request()->ip();


                    //修改商户余额
                    //$new_money = bcsub($merchant['money'],$params['amount'],3);
                    //$new_rate_money = bcsub($merchant['rate_money'],$fees,3);
                    $new_money = bcsub($merchant['money'],bcadd($params['amount'],$fees,2),2);

                    Db::name('merchant')->where('id',$merchant['id'])->update(['money'=>$new_money]);

                    //添加到余额记录表
                    //写入记录表
                    $logData['agent_id'] = $merchant['agent_id'];
                    $logData['mer_id'] = $merchant['id'];
                    $logData['out_trade_no'] = $params['out_trade_no'];
                    $logData['amount'] = $params['amount'];
                    $logData['before_amount'] = $merchant['money'];
                    $after = bcsub($merchant['money'],$params['amount'],2);
                    $logData['after_amount'] = $after;
                    $logData['type'] = 0;
                    $logData['create_time'] = time();
                    $logData['remark'] = '提交代付单扣款';
                    Db::name('money_log')->insert($logData);


                    //写入记录表-手续费
                    $logData2['agent_id'] = $merchant['agent_id'];
                    $logData2['mer_id'] = $merchant['id'];
                    $logData2['out_trade_no'] = $params['out_trade_no'];
                    $logData2['amount'] = $fees;
                    $logData2['before_amount'] = $after;
                    $logData2['after_amount'] = $new_money;
                    $logData2['type'] = 0;
                    $logData2['create_time'] = time();
                    $logData2['remark'] = '手续费扣款';
                    Db::name('money_log')->insert($logData2);


                    $result = $this->model->allowField(true)->save($params);


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
     * 批量添加
     */
    public function batch_add()
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


                    $findmerchant = Db::name('merchant')->where('id',$this->auth->id)->find();

                    if($findmerchant['status'] == 'hidden'){
                        $this->error(__('账号已被禁用'));
                    }

                    //判断是否开启谷歌验证
                    if (Config::get('fastadmin.order_checkmerchantgoogle')) {
                        $google = new GoogleAuthenticator();
                        $result = $google->verifyCode($findmerchant['google_code'],$params['google_captcha']);
                        if(!$result){
                            $this->error('谷歌验证码错误');
                        }
                    }

                    if($findmerchant['pay_password'] != md5($params['pay_pwd'])){
                        $this->error(__('支付密码错误'));
                    }


					//随机取一个下发用户
                    $users = Db::name('user')->where(['status'=>1,'is_receive'=>1])->select();
                    $users_count = count($users);
                    if($users_count <1){
                        $this->error(__('暂无可用下发人员，请联系管理员'));
                    }
                    $userList = $users[mt_rand(0, $users_count - 1)];


					$orderjson = $params['orderjson'];
					if(empty($orderjson)){
						$this->error(__('请填写代付信息'));
					}
                    $orderjson = json_decode($orderjson,true);

                    foreach ($orderjson as $key => $value){

                    	if($value['amount'] <1){
                        	continue;
                    	}

                    	//找出商户
                    	$merchant = Db::name('merchant')->where('id',$this->auth->id)->find();

                    	//生成订单号
                    	$out_trade_no = $this->buildnumber();
                    	$insertdata['out_trade_no'] = $out_trade_no;
                    	$insertdata['user_id'] = $userList['id'];
	                    $insertdata['mer_id'] = $merchant['id'];
	                    $insertdata['agent_id'] = $merchant['agent_id'];
	                    $insertdata['amount'] = $value['amount'];

	                    $fees = bcmul($value['amount'],$merchant['rate'],2);//手续费
	                    $fees = bcadd($fees,$merchant['add_money'],2);
	                    $insertdata['fees'] = $fees;

	                    if($merchant['money'] < bcadd($fees,$value['amount'],2)){
                            $this->error(__('余额不足支付此次订单，请先充值'));
                        }


						$insertdata['bank_number'] =$value['bank_number'];
						$insertdata['bank_type'] = $value['bank_type'];
						$insertdata['bank_user'] = $value['bank_user'];
	                    $insertdata['createtime'] = time();
	                    $insertdata['acount'] = $userList['username'];

						$result = Db::name('order')->insert($insertdata);

	                    //修改商户余额
                        $new_money = bcsub($merchant['money'],bcadd($value['amount'],$fees,3),3);

                        Db::name('merchant')->where('id',$merchant['id'])->update(['money'=>$new_money]);

	                    //添加到余额记录表
                        $logData['agent_id'] = $merchant['agent_id'];
                        $logData['mer_id'] = $merchant['id'];
                        $logData['out_trade_no'] = $out_trade_no;
                        $logData['amount'] = $value['amount'];
                        $logData['before_amount'] = $merchant['money'];
                        $after = bcsub($merchant['money'],$value['amount'],3);
                        $logData['after_amount'] = $after;
                        $logData['type'] = 0;
                        $logData['create_time'] = time();
                        $logData['remark'] = '提交代付单扣款';
                        Db::name('money_log')->insert($logData);


                        //写入记录表-手续费
                        $logData2['agent_id'] = $merchant['agent_id'];
                        $logData2['mer_id'] = $merchant['id'];
                        $logData2['out_trade_no'] = $out_trade_no;
                        $logData2['amount'] = $fees;
                        $logData2['before_amount'] = $after;
                        $logData2['after_amount'] = $new_money;
                        $logData2['type'] = 0;
                        $logData2['create_time'] = time();
                        $logData2['remark'] = '手续费扣款';
                        Db::name('money_log')->insert($logData2);

	                    sleep(1);

                    }

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
                    $this->error(__('未收到提交信息'));
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
        $row['user_id'] = Db::name('user')->where('id',$row['user_id'])->value('username');
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }


    //生成流水号
    public function buildnumber(){
        //19位订单号
        $number = date("YmdHis") . mt_rand(10000,99999);
        $re = Db::name('order')->where('out_trade_no',$number)->find();
        if($re){
            return $this->buildnumber();
        }
        return $number;
    }
}
