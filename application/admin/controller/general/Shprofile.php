<?php

namespace app\admin\controller\general;

use app\admin\model\Admin;
use app\common\controller\Backend;
use fast\Random;
use think\Db;
use think\Session;
use think\Validate;

/**
 * 个人配置
 *
 * @icon fa fa-user
 */
class Shprofile extends Backend
{

    /**
     * 查看
     */
    public function index()
    {
        //统计商户余额
        $sh_allmoney = Db::name('applys')->where(['admin_id'=>$this->auth->id,'status'=>1])->sum('amount');
        //统计提交了的单子的金额
        $sh_ordermoney = Db::name('sh_order')->where(['admin_id'=>$this->auth->id])->sum('amount');

        $sh_money  = bcsub($sh_allmoney,$sh_ordermoney,2);
        $this->view->assign('sh_money', $sh_money);

        return $this->view->fetch();
    }

    /**
     * 更新个人信息
     */
    public function update()
    {
        if ($this->request->isPost()) {
            $this->token();
            $params = $this->request->post("row/a");
            $params = array_filter(array_intersect_key(
                $params,
                array_flip(array('nickname', 'password','oldpassword', 'avatar','pay_password','pay_oldpassword'))
            ));
            unset($v);
            unset($params['money']);
            /*if (!Validate::is($params['email'], "email")) {
                $this->error(__("Please input correct email"));
            }*/
            $admin = Admin::get($this->auth->id);

            if (isset($params['password'])) {
                if (!Validate::is($params['password'], "/^[\S]{6,16}$/")) {
                    $this->error(__("Please input correct password"));
                }

                if(md5(md5($params['oldpassword']) . $admin['salt']) != $admin['password']){
                    $this->error(__("旧密码错误"));
                }

                $params['salt'] = Random::alnum();
                $params['password'] = md5(md5($params['password']) . $params['salt']);
            }
            if(isset($params['pay_password'])){
                if (!Validate::is($params['pay_password'], "/^[\S]{6,16}$/")) {
                    $this->error(__("Please input correct password"));
                }

                if(md5($params['pay_oldpassword']) != $admin['pay_password']){
                    $this->error(__("旧支付密码错误"));
                }

                $params['pay_password'] = md5($params['pay_password']);
            }
			unset($params['oldpassword']);
			unset($params['pay_oldpassword']);
            /*$exist = Admin::where('email', $params['email'])->where('id', '<>', $this->auth->id)->find();
            if ($exist) {
                $this->error(__("Email already exists"));
            }*/
            if ($params) {
                $admin = Admin::get($this->auth->id);
                $admin->save($params);
                //因为个人资料面板读取的Session显示，修改自己资料后同时更新Session
                Session::set("admin", $admin->toArray());
                $this->success();
            }
            $this->error();
        }
        return;
    }
}
