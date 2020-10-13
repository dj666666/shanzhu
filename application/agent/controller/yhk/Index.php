<?php

namespace app\agent\controller\yhk;

use app\common\controller\AgentBackend;
use think\Db;

/**
 * 银行卡管理
 *
 * @icon fa fa-circle-o
 */
class Index extends AgentBackend
{

    /**
     * Index模型对象
     * @var \app\agent\model\yhk\Index
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\agent\model\yhk\Index;
        $this->view->assign("isStateList", $this->model->getIsStateList());
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
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            foreach($list as &$key){
            	$key['remainmoney'] = bcsub(50000,$key['money'],2);
            }
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    public function getbank(){
    	//当前页
        $page = $this->request->request('pageNumber', 1, 'int');
        //分页大小
        $pagesize = $this->request->request('pageSize');

    	if ($this->request->request('keyField')) {
    		$where = [];
            $datalist = $this->model->where($where)
                ->page($page, $pagesize)
                ->field('*')
                ->select();
            $count = $this->model->where($where)->count();
            foreach ($datalist as $index => $item) {
                unset($item['password'], $item['salt']);
                $item = $item->toArray();
                $list[] = [
                	'id'	=> $item['id'],
                   	'bank_number' => $item['bank_user'].'----'.$item['bank_name'].'----'.$item['bank_number'].'----'.'剩余额度:'.$item['money'],
                ];
            }

            return json(['list' => $list, 'total' => $count]);
    	}
    }


	public function resetMoney($ids = null){
		if(!$ids){
			$this->error(__('参数缺少'));
		}

		$re = Db::name('yhk')->where('id',$ids)->update(['money'=>'50000.00']);

		if($re){
			$this->success('重置额度成功');
		}else{
            $this->success('重置额度失败');
        }
	}

}
