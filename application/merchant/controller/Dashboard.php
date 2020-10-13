<?php

namespace app\merchant\controller;

use app\common\controller\MerchantBackend;
use think\Config;
use think\Db;

/**
 * 控制台
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends MerchantBackend
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


        $where = ['mer_id'=>$this->auth->id ,'status'=>1];
        $where2 = ['mer_id'=>$this->auth->id ,'status'=>4];
        $where3 = ['mer_id'=>$this->auth->id ,'status'=>5];


        $today_money = Db::name('order')->where($where)->whereTime('createtime', 'today')->sum('amount');//今日总额
        $today_order = Db::name('order')->where($where)->whereTime('createtime', 'today')->count();		//今日订单数量
        $today_faile = Db::name('order')->where($where2)->whereTime('createtime', 'today')->count();		//今日失败订单数量

        $yesterday_money = Db::name('order')->where($where)->whereTime('createtime', 'yesterday')->sum('amount'); //昨日总额
        $yesterday_order = Db::name('order')->where($where)->whereTime('createtime', 'yesterday')->count();		 //昨日订单数量
        $yesterday_faile = Db::name('order')->where($where2)->whereTime('createtime', 'yesterday')->count();		 //昨日失败订单数量

		$all_success_money = Db::name('order')->where($where)->sum('amount'); //总额
        $all_success_order = Db::name('order')->where($where)->count();	//总订单数量
        $all_fail_order = Db::name('order')->where($where2)->count();		 //总失败订单数量


        $merchant = Db::name('merchant')->where(['id'=>$this->auth->id])->find();		 //余额
        //$blocking = Db::name('applys')->where($where3)->sum('amount');		 //冻结金额

        $kydj = bcadd($merchant['block_money'] ,$merchant['money'],3);
        
        
        $today_fees = Db::name('order')->where($where)->whereTime('createtime', 'today')->sum('fees');//今日手续费
        $yesterday_fees = Db::name('order')->where($where)->whereTime('createtime', 'yesterday')->sum('fees');		//昨日手续费
        $all_fees = Db::name('order')->where($where)->sum('fees');		//总手续费
        

        $this->view->assign([
            'today_fees'        => $today_fees,
            'yesterday_fees'        => $yesterday_fees,
            'all_fees'        => $all_fees,
            'kydj'        => $kydj,
            'balance'        => $merchant['money'],
            'blocking'        => $merchant['block_money'],
            'today_money'        => $today_money,
            'today_order'        => $today_order,
            'today_faile'        => $today_faile,
            'yesterday_money'       => $yesterday_money,
            'yesterday_order'       => $yesterday_order,
            'yesterday_faile'       => $yesterday_faile,
            'all_success_money'     	=> $all_success_money,
            'all_success_order'     	=> $all_success_order,
            'all_fail_order'     	=> $all_fail_order,
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

}
