<?php

namespace app\user\controller\yhk;

use app\common\controller\UserBackend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 商户申请充值记录管理
 *
 * @icon fa fa-circle-o
 */
class Rate extends UserBackend
{
    
    /**
     * Rate模型对象
     * @var \app\user\model\yhk\Rate
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\user\model\yhk\Rate;
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
                    ->with(['merchant'])
                    ->where('rate.user_id',$this->auth->id)
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with(['merchant'])
                    ->where('rate.user_id',$this->auth->id)
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            foreach ($list as $row) {
                
                $row->getRelation('merchant')->visible(['username']);
				//$row->getRelation('user')->visible(['username']);
				//$row->getRelation('yhk')->visible(['bank_user','bank_name','bank_number','bank_type']);
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

                    /*if($params['amount'] < 1){
                    	$this->error(__('充值金额必须大于1'));
                    }*/

                    $yhkId = $params['banknumber'];

                    //找出这张卡属于哪个用户
                    $findyhk =Db::name('yhk')->where('id',$yhkId)->field('user_id,money,bank_user,bank_name,bank_number,bank_type')->find();

                    $params['yhk_id'] = $yhkId;
                    $params['user_id'] = $findyhk['user_id'];
                    $params['mer_id'] = $this->auth->id;
                    $params['agent_id'] = $this->auth->agent_id;

                    $params['out_trade_no'] = $this->buildnumber();
                    unset($params['banknumber']);

                    $params['bank_user'] = $findyhk['bank_user'];
                    $params['bank_name'] = $findyhk['bank_name'];
                    $params['bank_number'] = $findyhk['bank_number'];
                    $params['bank_type'] = $findyhk['bank_type'];

                    $result = $this->model->allowField(true)->save($params);

                    //减去银行卡余额
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


    //收到申请充值金额
    public function received($ids = null){
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }

        //找出商户
        $findmerchant = Db::name('merchant')->where('id',$row['mer_id'])->field('id,agent_id,rate_money')->find();



            //收到了就加余额 如果没更新过该订单 就说明是第一次点
            if(empty($row['updatetime'])){

                if($row['amount'] >1){
                    //更新订单状态
                    $updateData['updatetime'] = time();
                    $updateData['status'] = 1;
                    $result = $row->allowField(true)->save($updateData);

                    //商户手续费余额增加
                    $new_money = bcadd($findmerchant['rate_money'],$row['amount'],3);
                    $re = Db::name('merchant')->where('id',$findmerchant['id'])->update(['rate_money'=>$new_money]);

                    //写入记录表
                    $logData['agent_id'] = $findmerchant['agent_id'];
                    $logData['mer_id'] = $findmerchant['id'];
                    $logData['out_trade_no'] = $row['out_trade_no'];
                    $logData['amount'] = $row['amount'];
                    $logData['before_amount'] = $findmerchant['rate_money'];
                    $after = bcadd($findmerchant['rate_money'],$row['amount'],3);
                    $logData['after_amount'] = $after;
                    $logData['type'] = 1;
                    $logData['create_time'] = time();
                    $logData['remark'] = '手续费充值';

                    Db::name('rate_money_log')->insert($logData);

                }else{
                    //更新订单状态
                    $updateData['updatetime'] = time();
                    $updateData['status'] = 1;
                    $result = $row->allowField(true)->save($updateData);

                    //提交负数单子，商户手续费余额减少
                    $new_money = bcadd($findmerchant['rate_money'],$row['amount'],3);
                    $re = Db::name('merchant')->where('id',$findmerchant['id'])->update(['rate_money'=>$new_money]);

                    //写入记录表
                    $logData['agent_id'] = $findmerchant['agent_id'];
                    $logData['mer_id'] = $findmerchant['id'];
                    $logData['out_trade_no'] = $row['out_trade_no'];
                    $logData['amount'] = $row['amount'];
                    $logData['before_amount'] = $findmerchant['money'];
                    $after = bcadd($findmerchant['rate_money'],$row['amount'],3);
                    $logData['after_amount'] = $after;
                    $logData['type'] = 0;
                    $logData['create_time'] = time();
                    $logData['remark'] = '手续费主动扣款';

                    Db::name('rate_money_log')->insert($logData);
                }


            }
        /*// 启动事务
        Db::startTrans();

        try {
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }*/

        if($result && $re){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }


    }


    //未收到申请充值金额
    public function notreceived($ids = null){
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }

        // 启动事务
        Db::startTrans();
        try {

            //未收到 如果没更新过该订单 就说明是第一次点
            if(empty($row['updatetime'])){

                //更新订单状态
                $updateData['updatetime'] = time();
                $updateData['status'] = 2;
                $result = $row->allowField(true)->save($updateData);

            }

            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }

        if($result){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }


    //冻结
    /*public function dongjie($ids){
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }

        $result = false;



        //更新订单状态
        $updateData['updatetime'] = time();
        $updateData['status'] = 5;
        $result = $row->allowField(true)->save($updateData);

        // 启动事务
        Db::startTrans();
        try{    // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }


        if($result){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }


    }*/


}
