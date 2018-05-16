<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use User\Api\UserApi;

/**
 * 用户控制器
 * 包括用户中心，用户登录及注册
 */
class UserController extends HomeController {


	/* 注册页面 */
	public function register(){
        
			$this->display();
	}
	public function doregister(){
		$username = $mobile = I('post.mobile');
		$password = I('post.password');
		$verify = I('post.verify');
		$cname = I('post.username');
		$post=I('post.');
		if(!check_verify($verify)){
			$data=array('status'=>0,'message'=>'验证码错误！');
			$this->ajaxReturn($data);exit;
		}
		/* 调用注册接口注册用户 */
		$User = new UserApi;
		$uid = $User->register($username, $password, $mobile,$cname);
		if($uid > 0){
			$user = array('uid' => $uid, 'nickname' => $username,'cname' => $cname, 'moblie'=>$mobile, 'status' => 1);
			if(M('Member')->add($user)){
				$data=array('status'=>1,'message'=>'注册成功,请等待审核通过,即可登录平台！');
				$this->ajaxReturn($data);exit;
			}
		}else{
			$data=array('status'=>0,'message'=>$this->showRegError($uid));
			$this->ajaxReturn($data);exit;
		}
	}
	/* 登录页面 */
	public function login(){
		//dump(session('user_auth'));
		$this->display();
		
	}
	public function dologin(){
		$username = I('post.mobile');
		$password = I('post.password');
		$map = array('mobile'=>$username);

		/* 调用UC登录接口登录 */
		$user = new UserApi;		
		$uid = $user->login($username, $password);
		if($uid < 0){
			$err = $this->showLoginError($uid);
			$data=array('status'=>0,'message'=>$err);
			$this->ajaxReturn($data);exit;
		}
		
		$flag = D('Member')->login($uid);
		if(!$flag){
			$data=array('status'=>0,'message'=>'登录失败，请重试！');
			$this->ajaxReturn($data);exit;
		}
		$data=array('status'=>1,'message'=>'登录成功');
		$this->ajaxReturn($data);exit;

	}
	/* 退出登录 */
	public function logout(){
		if(is_login()){
			D('Member')->logout();
			$this->success('退出成功！', U('User/login'));
		} else {
			$this->redirect('User/login');
		}
	}

	/* 验证码，用于登录和注册 */
	public function verify(){
		$verify = new \Think\Verify();
		$verify->entry(1);
	}

	/**
	 * 获取用户注册错误信息
	 * @param  integer $code 错误编码
	 * @return string        错误信息
	 */
	private function showRegError($code = 0){
		switch ($code) {
			case -1:  $error = '用户名长度必须在16个字符以内！'; break;
			case -2:  $error = '用户名被禁止注册！'; break;
			case -3:  $error = '手机号被占用！'; break;
			case -4:  $error = '密码长度必须在6-30个字符之间！'; break;
			case -5:  $error = '邮箱格式不正确！'; break;
			case -6:  $error = '邮箱长度必须在1-32个字符之间！'; break;
			case -7:  $error = '邮箱被禁止注册！'; break;
			case -8:  $error = '邮箱被占用！'; break;
			case -9:  $error = '手机格式不正确！'; break;
			case -10: $error = '手机被禁止注册！'; break;
			case -11: $error = '手机号被占用！'; break;
			default:  $error = '未知错误';
		}
		return $error;
	}
	/**
	 * 获取用户注册错误信息
	 * @param  integer $code 错误编码
	 * @return string        错误信息
	 */
	private function showLoginError($code = 0){
		switch ($code) {
			case -1:  $error = '用户不存在或未通过审核！'; break;
			case -2:  $error = '用户或密码错误！'; break;
		}
		return $error;
	}


    /**
     * 修改密码提交
     * @author huajie <banhuajie@163.com>
     */
    public function profile(){
		if ( !is_login() ) {
			$this->error( '您还没有登陆',U('User/login') );
		}
        if ( IS_POST ) {
            //获取参数
            $uid        =   is_login();
            $password   =   I('post.old');
            $repassword = I('post.repassword');
            $data['password'] = I('post.password');
            empty($password) && $this->error('请输入原密码');
            empty($data['password']) && $this->error('请输入新密码');
            empty($repassword) && $this->error('请输入确认密码');

            if($data['password'] !== $repassword){
                $this->error('您输入的新密码与确认密码不一致');
            }

            $Api = new UserApi();
            $res = $Api->updateInfo($uid, $password, $data);
            if($res['status']){
                $this->success('修改密码成功！');
            }else{
                $this->error($res['info']);
            }
        }else{
            $this->display();
        }
    }

}
