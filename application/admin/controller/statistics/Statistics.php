<?php

namespace app\admin\controller\statistics;

use app\common\controller\Backend;
use think\Db;
/**
 * 会员管理
 *
 * @icon fa fa-user
 */
class Statistics extends Backend
{

    /**
     * Statistics模型对象
     * @var \app\admin\model\statistics\Statistics
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\statistics\Statistics;
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign("isReceiveList", $this->model->getIsReceiveList());
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
        $this->relationSearch = false;
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

            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                    //->where($where)
                    ->order($sort, $order)
                    ->count();

            $list = $this->model
                    //->where($where)
                    ->order($sort, $order)
                    ->limit($offset, $limit)
                    ->select();

            foreach ($list as $row) {
                $row->visible(['id','username','all_money','all_success_money','all_faile_money','all_order','all_success_order','all_faile_order']);

                //该用户总金额
                $row['all_money'] = Db::name('order')->where(['user_id'=>$row['id']])->where($map)->sum('amount');
                //该用户成功总金额
                $row['all_success_money'] = Db::name('order')->where(['user_id'=>$row['id'],'status'=>1])->where($map)->sum('amount');
                //该用户异常总金额
                $row['all_faile_money'] = Db::name('order')->where(['user_id'=>$row['id']])->where('status','<>',1)->where($map)->sum('amount');

                //该用户总订单数量
                $row['all_order'] = Db::name('order')->where(['user_id'=>$row['id']])->where($map)->count();
                //该用户总成功订单数量
                $row['all_success_order'] = Db::name('order')->where(['user_id'=>$row['id'],'status'=>1])->where($map)->count();
                //该用户总失败订单数量
                $row['all_faile_order'] = Db::name('order')->where(['user_id'=>$row['id']])->where('status','<>',1)->where($map)->count();


            }
            $list = collection($list)->toArray();

            //总金额
            $allmoney = Db::name('order')->where($map)->sum('amount');
            $allsuccessmoney = Db::name('order')->where(['status'=>1])->where($map)->sum('amount');
            $allfailemoney = Db::name('order')->where('status','<>',1)->where($map)->sum('amount');
            //总订单数量
            $allorder = Db::name('order')->where($map)->count();
            //总成功数量
            $allsuccessorder = Db::name('order')->where(['status'=>1])->where($map)->count();
            //总异常数量
            $allfaileorder = Db::name('order')->where('status','<>',1)->count();
            //总手续费
            $allfees = Db::name('order')->where(['status'=>1])->where($map)->sum('fees');


            $result = array("total" => $total, "rows" => $list, "extend" => ['allmoney' => $allmoney,
                'allsuccessmoney' => $allsuccessmoney,
                'allfailemoney' => $allfailemoney,
                'allorder' => $allorder,
                'allsuccessorder' => $allsuccessorder,
                'allfaileorder' => $allfaileorder,
                'allfees' => $allfees,

            ]);

            return json($result);
        }
        return $this->view->fetch();
    }
}
