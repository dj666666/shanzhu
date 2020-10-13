<?php

namespace app\merchant\controller\merchant;

use app\common\controller\MerchantBackend;

/**
 * 商户管理
 *
 * @icon fa fa-circle-o
 */
class Merchant extends MerchantBackend
{
    
    /**
     * Merchant模型对象
     * @var \app\merchant\model\merchant\Merchant
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\merchant\model\merchant\Merchant;

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
                $row->visible(['id','group_id','agent_id','number','username','nickname','rate','logintime','loginip','jointime','status']);
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
}
