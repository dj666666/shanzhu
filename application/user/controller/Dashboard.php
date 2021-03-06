<?php

namespace app\user\controller;

use app\common\controller\UserBackend;
use think\Config;
use think\Db;

/**
 * 控制台
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends UserBackend
{

    /**
     * 查看
     */
    public function index()
    {
        $seventtime = \fast\Date::unixtime('day', -7);
        $paylist = $createlist = [];
        for ($i = 0; $i < 7; $i++)
        {
            $day = date("Y-m-d", $seventtime + ($i * 86400));
            $createlist[$day] = mt_rand(20, 200);
            $paylist[$day] = mt_rand(1, mt_rand(1, $createlist[$day]));
        }
        $hooks = config('addons.hooks');
        $uploadmode = isset($hooks['upload_config_init']) && $hooks['upload_config_init'] ? implode(',', $hooks['upload_config_init']) : 'local';
        $addonComposerCfg = ROOT_PATH . '/vendor/karsonzhang/fastadmin-addons/composer.json';
        Config::parse($addonComposerCfg, "json", "composer");
        $config = Config::get("composer");
        $addonVersion = isset($config['version']) ? $config['version'] : __('Unknown');


        $where = ['user_id'=>$this->auth->id ,'status'=>1];
        $where2 = ['user_id'=>$this->auth->id ,'status'=>4];


        $today_money = Db::name('order')->where($where)->whereTime('createtime', 'today')->sum('amount');//今日总额
        $today_order = Db::name('order')->where($where)->whereTime('createtime', 'today')->count();		//今日订单数量
        $today_faile = Db::name('order')->where($where2)->whereTime('createtime', 'today')->count();		//今日失败订单数量
        
        //今日手续费
        $today_fees = Db::name('order')->where($where)->whereTime('createtime', 'today')->sum('fees');
        //昨日手续费
        $yesterday_fees = Db::name('order')->where($where)->whereTime('createtime', 'yesterday')->sum('fees');
        //总手续费
        $all_fees = Db::name('order')->where($where)->sum('fees');
            
          
        
        $yesterday_money = Db::name('order')->where($where)->whereTime('createtime', 'yesterday')->sum('amount'); //昨日总额
        $yesterday_order = Db::name('order')->where($where)->whereTime('createtime', 'yesterday')->count();		 //昨日订单数量
        $yesterday_faile = Db::name('order')->where($where2)->whereTime('createtime', 'yesterday')->count();		 //昨日失败订单数量
        
        $all_money = Db::name('order')->where($where)->sum('amount'); //总额
        $all_order = Db::name('order')->where($where)->count();	//总订单数量
        $all_faile = Db::name('order')->where($where2)->count();		 //总失败订单数量


        $total_money = Db::name('order')->where(['user_id'=>$this->auth->id,'status'=>1])->sum('amount');
        $group = Db::name('user_group')->where(['id'=>$this->auth->group_id])->value('name');

        $user = Db::name('user')->where('id',$this->auth->id)->find();

        //当天7点时间
        $time = mktime(7,0,0,date('m'),date('d'),date('Y'));
        //前一天7点
        $old_time = mktime(7,0,0,date('m'),date('d')-1,date('Y'));
        //今日7点到第二天7点的订单金额
		$severn_money = Db::name('order')
    		->where(['user_id'=>$this->auth->id,'status'=>1])
    		->whereTime('createtime', 'between', [$old_time, $time])
    		->sum('amount'); 
    		
        //今日7点到第二天7点的已收金额
        $severn_banK_money = Db::name('applys')
        		->where(['user_id'=>$this->auth->id,'status'=>1])
        		->whereTime('createtime', 'between', [$old_time, $time])
        		->sum('amount');
        
        $this->view->assign([
            'all_fees' => $all_fees,
            'today_fees' => $today_fees,
            'yesterday_fees' => $yesterday_fees,
            'severn_banK_money' => $severn_banK_money,
            'severn_money'      => $severn_money,
            'is_receive'        => $user['is_receive'],
            'today_money'      => $today_money,
            'today_order'      => $today_order,
            'today_faile'      => $today_faile,
            'yesterday_money'  => $yesterday_money,
            'yesterday_order'  => $yesterday_order,
            'number'           => $this->auth->number,
            'username'         => $this->auth->username,
            'nickname'         => $this->auth->nickname,
            'quota'            => $total_money,
            'group'            => $group,
            'total_money'      => $total_money,
            'totaluser'        => 35200,
            'totalviews'       => 219390,
            'totalorder'       => 32143,
            'totalorderamount' => 174800,
            'todayuserlogin'   => 321,
            'todayusersignup'  => 430,
            'todayorder'       => 2324,
            'unsettleorder'    => 132,
            'sevendnu'         => '80%',
            'sevendau'         => '32%',
            'paylist'          => $paylist,
            'createlist'       => $createlist,
            'addonversion'       => $addonVersion,
            'uploadmode'       => $uploadmode
        ]);

        return $this->view->fetch();
    }

    //更改用户接单状态
    public function changeuser(){
        $user = Db::name('user')->where('id',$this->auth->id)->find();
        if($user){

            $is_receive = $user['is_receive'] == 1 ? 2 : 1;

            $re = Db::name('user')->where('id',$user['id'])->update(['is_receive'=>$is_receive]);

            if($re){
                $this->success('修改成功');
            }else{
                $this->error(__('修改失败，请联系管理员'));
            }
        }else{
            $this->error(__('非法操作'));
        }

    }
}
