<?php

namespace app\user\controller\order;

use app\common\controller\UserBackend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 订单管理
 *
 * @icon fa fa-circle-o
 */
class Index extends UserBackend
{

    /**
     * Index模型对象
     * @var \app\user\model\order\Index
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\user\model\order\Index;
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

            $filter = json_decode($this->request->get('filter'));

            $op = json_decode($this->request->get('op'));

            $filter = collection($filter)->toArray();

            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            if(isset($filter['status']) && $filter['status'] == '2'){
                $offset= 0;
                $limit = 1;
            }

            $total = $this->model
                    ->with(['user'])
                    ->where('user_id',$this->auth->id)
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with(['user'])
                    ->where('user_id',$this->auth->id)
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            foreach ($list as $row) {

                $row->getRelation('user')->visible(['username']);
            }
            $list = collection($list)->toArray();

            //去查询有没有新的充值订单
            $findapply = Db::name('applys')->where('status',4)->order('createtime desc')->find();

            if($findapply){
                $result = array("total" => $total, "rows" => $list,"extend"=>[
                    'findapply' => 1
                ]);
            }else{
                $result = array("total" => $total, "rows" => $list,"extend"=>[
                    'findapply' => 2
                ]);
            }


            return json($result);
        }
        $this->assignconfig('uid',$this->auth->id);

        return $this->view->fetch();
    }



    public function xiafa($ids){
        if(!$ids){
            $this->error(__('参数缺少'));
        }
        $order = Db::name('order')->where('id',$ids)->field('id,mer_id,agent_id,out_trade_no,amount')->find();

        if($order){

            // 启动事务
            Db::startTrans();

            try{
                $re = Db::name('order')->where('id',$ids)->update(['status'=>1,'ordertime'=>time()]);

                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }


            if($re){
                $this->success('下发成功');
            }else{
                $this->error('下发失败,联系管理员');
            }
        }

        $this->error('订单不存在');

    }

    public function yichang($ids){
        if(!$ids){
            $this->error(__('参数缺少'));
        }
        $order = Db::name('order')->where('id',$ids)->field('id,out_trade_no,mer_id,agent_id,amount,fees')->find();

        if($order){


            // 启动事务
            Db::startTrans();

            try{

                $re = Db::name('order')->where('id',$ids)->update(['status'=>4,'ordertime'=>time()]);

                //找出商户
                $findmerchant = Db::name('merchant')->where('id',$order['mer_id'])->field('id,agent_id,rate,rate_money,money,add_money')->find();

                //手续费
                $fees = bcmul($order['amount'],$findmerchant['rate'],2);//手续费
                $fees = bcadd($fees,$findmerchant['add_money'],2); //加

                //商户余额退回
                //$new_money = bcadd($findmerchant['money'],$order['amount'],3);
                //手续费退回
                //$new_rate_money = bcadd($findmerchant['rate_money'],$fees,3);
                //Db::name('merchant')->where('id',$findmerchant['id'])->update(['money'=>$new_money,'rate_money'=>$new_rate_money]);

                //商户余额退回
                $new_money = bcadd($findmerchant['money'],bcadd($order['amount'],$fees,2),2);
                Db::name('merchant')->where('id',$findmerchant['id'])->update(['money'=>$new_money]);



                //写入记录表
                $logData['agent_id'] = $findmerchant['agent_id'];
                $logData['mer_id'] = $findmerchant['id'];
                $logData['out_trade_no'] = $order['out_trade_no'];
                $logData['amount'] = $order['amount'];
                $logData['before_amount'] = $findmerchant['money'];
                $after = bcadd($findmerchant['money'],$order['amount'],3);
                $logData['after_amount'] = $after;
                $logData['type'] = 1;
                $logData['create_time'] = time();
                $logData['remark'] = '代付单异常退款';

                Db::name('money_log')->insert($logData);

                //写入记录表-手续费
                $logData2['agent_id'] = $findmerchant['agent_id'];
                $logData2['mer_id'] = $findmerchant['id'];
                $logData2['out_trade_no'] = $order['out_trade_no'];
                $logData2['amount'] = $fees;
                //$logData2['before_amount'] = $findmerchant['rate_money'];
                $logData2['before_amount'] = $after;
                $logData2['after_amount'] = $new_money;
                $logData2['type'] = 1;
                $logData2['create_time'] = time();
                $logData2['remark'] = '手续费退款';

                Db::name('money_log')->insert($logData2);

                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }

            if($re){
                $this->success('操作成功');
            }else{
                $this->error('操作失败');
            }

        }

        $this->error('订单不存在');


    }

    public function huichong($ids){
        if(!$ids){
            $this->error(__('参数缺少'));
        }
        $order = Db::name('order')->where('id',$ids)->field('id,out_trade_no,mer_id,agent_id,amount,fees')->find();

        if($order){
            // 启动事务
            Db::startTrans();

            try{

                $re = Db::name('order')->where('id',$ids)->update(['status'=>5,'ordertime'=>time()]);

                //找出商户
                $findmerchant = Db::name('merchant')->where('id',$order['mer_id'])->field('id,agent_id,rate,rate_money,money,add_money')->find();

                //手续费
                $fees = bcmul($order['amount'],$findmerchant['rate'],2);//手续费
                $fees = bcadd($fees,$findmerchant['add_money'],2); //加

                //商户余额退回
                //$new_money = bcadd($findmerchant['money'],$order['amount'],3);
                //手续费退回
                //$new_rate_money = bcadd($findmerchant['rate_money'],$fees,3);

                //Db::name('merchant')->where('id',$findmerchant['id'])->update(['money'=>$new_money,'rate_money'=>$new_rate_money]);

                //商户余额退回
                $new_money = bcadd($findmerchant['money'],bcadd($order['amount'],$fees,2),2);
                Db::name('merchant')->where('id',$findmerchant['id'])->update(['money'=>$new_money]);

                //写入记录表
                $logData['agent_id'] = $findmerchant['agent_id'];
                $logData['mer_id'] = $findmerchant['id'];
                $logData['out_trade_no'] = $order['out_trade_no'];
                $logData['amount'] = $order['amount'];
                $logData['before_amount'] = $findmerchant['money'];
                $after = bcadd($findmerchant['money'],$order['amount'],2);
                $logData['after_amount'] = $after;
                $logData['type'] = 1;
                $logData['create_time'] = time();
                $logData['remark'] = '代付单回充退款';

                Db::name('money_log')->insert($logData);

                //写入记录表-手续费
                $logData2['agent_id'] = $findmerchant['agent_id'];
                $logData2['mer_id'] = $findmerchant['id'];
                $logData2['out_trade_no'] = $order['out_trade_no'];
                $logData2['amount'] = $fees;
                $logData2['before_amount'] = $after;
                $logData2['after_amount'] = $new_money;
                $logData2['type'] = 1;
                $logData2['create_time'] = time();
                $logData2['remark'] = '手续费退款';

                Db::name('money_log')->insert($logData2);

                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }

            if($re){
                $this->success('操作成功');
            }else{
                $this->error('操作失败');
            }

        }

        $this->error('订单不存在');


    }


}
