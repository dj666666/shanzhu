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
class Apply extends UserBackend
{

    /**
     * Apply模型对象
     * @var \app\user\model\yhk\Apply
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\user\model\yhk\Apply;
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
                ->with(['yhk','merchant'])
                ->where('apply.user_id',$this->auth->id)
                ->where($where)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->with(['yhk','merchant'])
                ->where('apply.user_id',$this->auth->id)
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            foreach ($list as $row) {

                $row->getRelation('yhk')->visible(['bank_user','bank_name','bank_number']);
				$row->getRelation('merchant')->visible(['username']);
            }
            $list = collection($list)->toArray();

            //今日已收金额
            $apply_money = Db::name('applys')->where(['user_id'=>$this->auth->id,'status'=>1])->where('amount','>',0)->whereTime('createtime', 'today')->sum('amount');
            //昨日已收金额
            $yesterday_apply_money = Db::name('applys')->where(['user_id'=>$this->auth->id,'status'=>1])->where('amount','>',0)->whereTime('createtime', 'yesterday')->sum('amount');
            //总收金额
            $all_apply_money = Db::name('applys')->where(['user_id'=>$this->auth->id,'status'=>1])->where('amount','>',0)->sum('amount');
            //总冻结金额
            //$all_f_apply_money = Db::name('applys')->where(['user_id'=>$this->auth->id,'status'=>5])->sum('amount');

            //找出商户余额
            $sh_money = Db::name('merchant')->where(['id'=>10])->field('money,block_money,rate_money')->find();

            
    
            //今日7点时间
            $time = mktime(7,0,0,date('m'),date('d'),date('Y'));
            //前一天7点
            $old_time = mktime(7,0,0,date('m'),date('d')-1,date('Y'));
            //今日7点到前一天7点的订单金额
    		$severn_money = Db::name('applys')
        		->where(['user_id'=>$this->auth->id,'status'=>1])
        		->whereTime('createtime', 'between', [$old_time, $time])
        		->sum('amount'); //总额



            $result = array("total" => $total, "rows" => $list, "extend" => [
                'apply_money' => $apply_money,
                'yesterday_apply_money'=>$yesterday_apply_money,
                'all_apply_money'=>$all_apply_money,
                'all_f_apply_money'=>$sh_money['block_money'],
                'sh_money'=>$sh_money['money'],
                'rate_money'=>$sh_money['rate_money']
                ]);


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

                    $params['updatetime'] = time();

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



    //收到申请充值金额
    public function received($ids = null){
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }

        //找出商户
        $findmerchant = Db::name('merchant')->where('id',$row['mer_id'])->field('id,agent_id,money')->find();

        // 启动事务
        Db::startTrans();

        try {

            //收到了就加余额 如果没更新过该订单 就说明是第一次点
            if(empty($row['updatetime'])){

                if($row['amount'] >1){
                    //更新订单状态
                    $updateData['updatetime'] = time();
                    $updateData['status'] = 1;
                    $result = $row->allowField(true)->save($updateData);

                    //商户余额增加
                    $new_money = bcadd($findmerchant['money'],$row['amount'],3);
                    $re = Db::name('merchant')->where('id',$findmerchant['id'])->update(['money'=>$new_money]);

                    //写入记录表
                    $logData['agent_id'] = $findmerchant['agent_id'];
                    $logData['mer_id'] = $findmerchant['id'];
                    $logData['out_trade_no'] = $row['out_trade_no'];
                    $logData['amount'] = $row['amount'];
                    $logData['before_amount'] = $findmerchant['money'];
                    $after = bcadd($findmerchant['money'],$row['amount'],3);
                    $logData['after_amount'] = $after;
                    $logData['type'] = 1;
                    $logData['create_time'] = time();
                    $logData['remark'] = '充值';

                    Db::name('money_log')->insert($logData);

                }else{
                    //更新订单状态
                    $updateData['updatetime'] = time();
                    $updateData['status'] = 1;
                    $result = $row->allowField(true)->save($updateData);

                    //提交负数单子，商户余额减少
                    $new_money = bcadd($findmerchant['money'],$row['amount'],3);
                    $re = Db::name('merchant')->where('id',$findmerchant['id'])->update(['money'=>$new_money]);

                    //写入记录表
                    $logData['agent_id'] = $findmerchant['agent_id'];
                    $logData['mer_id'] = $findmerchant['id'];
                    $logData['out_trade_no'] = $row['out_trade_no'];
                    $logData['amount'] = $row['amount'];
                    $logData['before_amount'] = $findmerchant['money'];
                    $after = bcadd($findmerchant['money'],$row['amount'],3);
                    $logData['after_amount'] = $after;
                    $logData['type'] = 0;
                    $logData['create_time'] = time();
                    $logData['remark'] = '主动扣款';

                    Db::name('money_log')->insert($logData);
                }


            }

            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }

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
    public function dongjie($ids){
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


    }
    
    
}
