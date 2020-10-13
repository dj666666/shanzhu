<?php

namespace app\admin\controller\order;

use app\common\controller\Backend;
use think\Db;
/**
 * 订单管理
 *
 * @icon fa fa-circle-o
 */
class Order extends Backend
{

    /**
     * Order模型对象
     * @var \app\admin\model\order\Order
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\order\Order;
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

            $filter =   json_decode($this->request->get('filter'));

            $op =   json_decode($this->request->get('op'));

            $filter = collection($filter)->toArray();

            $map = [];

            if(isset($filter['createtime'])){
                $createtime = explode(' - ',$filter['createtime']);
                $timeStr = strtotime($createtime[0]).','.strtotime($createtime[1]);

                $map['createtime']= ['between', $timeStr];

            }

            if(isset($filter['user.username'])){
                //找出用户 获取用户id
                $userId = Db::name('user')->where('username',$filter['user.username'])->value('id');
                $map['user_id']= ['eq',$userId];

            }

            if(isset($filter['merchant.username'])){
                //找出商户 获取商户id
                $merchantId = Db::name('merchant')->where('username',$filter['merchant.username'])->value('id');
                $map['mer_id']= ['eq',$merchantId];

            }

            if(isset($filter['agent.username'])){
                //找出商户 获取商户id
                $agentId = Db::name('agent')->where('username',$filter['agent.username'])->value('id');
                $map['agent_id']= ['eq',$agentId];

            }

            if(isset($filter['status'])){
                $map['status']= ['eq',$filter['status']];
            }

            if(isset($filter['out_trade_no'])){
                $map['out_trade_no']= ['eq',$filter['out_trade_no']];

            }


            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                    ->with(['agent','merchant','user'])
                    ->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    ->with(['agent','merchant','user'])
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            foreach ($list as $row) {

                $row->getRelation('agent')->visible(['username']);
				$row->getRelation('merchant')->visible(['username']);
				$row->getRelation('user')->visible(['username']);
            }
            $list = collection($list)->toArray();

            //总金额
            $allmoney = Db::name('order')->where($map)->sum('amount');

            //总金额
            $allfees = Db::name('order')->where($map)->sum('fees');

            //总订单数量
            $allorder = Db::name('order')->where($map)->count();

            $result = array("total" => $total, "rows" => $list, "extend" => ['allmoney' => $allmoney, 'allorder' => $allorder, 'allfees' => $allfees]);

            return json($result);
        }
        return $this->view->fetch();
    }
}
