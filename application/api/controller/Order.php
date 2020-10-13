<?php

namespace app\api\controller;

use app\common\controller\Api;
use fast\Random;
use think\Db;

/**
 * 示例接口
 */
class Order extends Api
{

    //如果$noNeedLogin为空表示所有接口都需要登录才能请求
    //如果$noNeedRight为空表示所有接口都需要验证权限才能请求
    //如果接口已经设置无需登录,那也就无需鉴权了
    //
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['getBank','test', 'test1'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['test2'];

    //获取当前挂卡信息
    public function getOrder()
    {
        
       
        $amount = $this->request->request('amount');
        $trade_no = $this->request->request('trade_no');//四方平台订单号
        $merId = $this->request->request('merId');//商户id
        $sign = $this->request->request('sign');//加密值
        $call_back_url = $this->request->request('call_back_url');//回调地址
        
        if(!$amount){
            $this->error('参数缺少');
        }
        $mysign = md5($amount.$trade_no.$key);
        if($mysign != $sign){
            $this->error('签名错误');
        }

        //随机取一个银行卡
        $count = Db::name('yhk')->where(['is_state'=>1])->select();
        $countYhk = count($count);
        $list = $count[mt_rand(0, $countYhk - 1)];

        //插入记录
        $data = array('yhk_id'=>$list['id'],
            'out_trade_no'=>$this->buildnumber(),
            'amount'=>$amount,
            'createtime'=>time(),
            'updatetime'=>time(),
            );

        $re = Db::name('applys')->insert($data);

        if($re){
            unset($list['id']);
            unset($list['createtime']);
            unset($list['remark']);
            unset($list['is_state']);
            $this->success('获取成功', $list);
        }else{
            $this->error('系统错误');
        }
    }

        
    //生成流水号
    public function buildnumber(){

        $number = date("YmdHis") . mt_rand(10000,99999);
        $re = Db::name('order')->where('out_trade_no',$number)->find();
        if($re){
            return $this->buildnumber();
        }
        return $number;
    }

    /**
     * 无需登录的接口
     *
     */
    public function test1()
    {
        $this->success('返回成功', ['action' => 'test1']);
    }

    /**
     * 需要登录的接口
     *
     */
    public function test2()
    {
        $this->success('返回成功', ['action' => 'test2']);
    }

    /**
     * 需要登录且需要验证有相应组的权限
     *
     */
    public function test3()
    {
        $this->success('返回成功', ['action' => 'test3']);
    }

}
