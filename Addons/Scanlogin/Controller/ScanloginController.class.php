<?php

namespace Addons\Scanlogin\Controller;
use Home\Controller\AddonsController;

class ScanloginController extends AddonsController{
    private $appid;
    private $secret;
    
    public function _initialize(){
		$addon_config = $this->getConfig();
		if($addon_config['isopen'] != 1){
		    $this->error('未开启扫码登录',U('user/login'));
		}
		$this->appid = $addon_config['appid'];
		$this->secret = $addon_config['appsecret'];
    }
    
    public function index(){
//        dump(is_login());exit;
        //扫码获取用户信息
        $code = I('code');
        $state = I('state');
        if($code == '' || $state != session('opstate')){    
            session('user_auth','null');       
            $this->error('请重新授权',U('user/login'));
            exit;
        }
        session('user_auth','null');
        if(is_login()){
            $this->success('您已登录，无需重复登录',U('index/index'));
             exit;
        } 
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->appid}&secret={$this->secret}&code={$code}&grant_type=authorization_code";
        $token = $this->getJson($url);
        if(isset($token['errcode'])){
            $this->error('未获取到access_token，请重新授权',U('user/login'));
            exit;
        }
        
        $url2 = "https://api.weixin.qq.com/sns/userinfo?access_token={$token['access_token']}&openid={$token['openid']}&lang=zh_CN";
        $info = $this->getJson($url2);
        if(isset($info['errcode'])){
            $this->error('未获取到用户信息，请重新授权',U('user/login'));
            exit;
        }
//        $info = array(
//            "openid"=>"oCNJ_uMpzlCqH4S0nWoALsI2u6cw",
//            "nickname"=>"让木头哭丶",
//            "sex"=>1,
//            "language"=>"zh_CN" ,
//            "city"=>"南昌",
//            "province"=>"江西",
//            "country"=>"中国",
//            "headimgurl"=>"http://wx.qlogo.cn/mmopen/AkcOrVMDfhMvobiaicF4RbhsZMlIicmYt2cxKEGNtVy4paiccEdzsUticB83QzBmvvVuX0T8CCKI2oicKYDCibZDujBNyZ4JIShzPNS/0",
//            "privilege"=>'',
//            "unionid"=>"ov2Rkt3HsVXid2dfr0Rvht1VNw1w"  
////                         ov2RktwGKZk2XDWy31lQSEdVIqz4
////                         ov2Rkt0a9uxT3zyN67HqttQJjWh8
//        );
        //扫码登录、注册
        $where['wx_unionid'] = $info['unionid'];
        $ucmember_model = M('UcenterMember');
        $user = $ucmember_model->where($where)->field('id,wx_unionid,wx_open_id,status')->find();
        if(empty($user)){
//        if(true){
            //注册
            require('./Application/User/Conf/config.php');
            require('./Application/User/Common/common.php');
            $ip = get_client_ip(1);
            $time = NOW_TIME;
            $username = 'wx_'.substr(strtolower($info['unionid']),-6).'_'.mt_rand(1000,9999);
            $password = $username.'wx';
            $data = array(
                'reg_time'=> $time,
                'reg_ip'   => $ip,
                'last_login_time' => $time,
                'last_login_ip'   => $ip,
                'wx_unionid'=> $info['unionid'],
                'wx_open_id'=> $info['openid'],
                'username'=>$username,
                'password'=>think_ucenter_md5($password,UC_AUTH_KEY),
                'status' => 1,
            );
//            dump($data);exit;
            $uid = $ucmember_model->data($data)->add();
            unset($data);
            
            if($uid<=0){
                $this->error('注册失败，请重试',U('user/loign'));
                exit;
            }
            
            $user_info = array(
                'id' => $uid,
                'nickname' => $username,
                'headimgurl' => $info['headimgurl'],
                'sex' => $info['sex'],
                'reg_time'=> $time,
                'reg_ip'   => $ip,
                'login' => 1,
                'status' => 1,
                'last_login_time' => $time,
                'last_login_ip'   => $ip,
            );
            $member_model = M('Member');
            $flag = $member_model->data($user_info)->add();
            if($flag==false){
                $this->error('注册信息写入失败，请联系管理员',U('user/loign'));
                exit;
            }
            $auth = array(
                'uid'             => $uid,
                'username'        => get_username($uid),
                'last_login_time' => $time,
            );
            session('user_auth', $auth);
            session('user_auth_sign', data_auth_sign($auth));
            $this->success('注册成功',U('index/index'));
        }else{
            //登录
            if(1 != $user['status']){
                $this->error('登录失败，该账号无法使用',U('index/index'));
                exit;
            }
            if(empty($user['wx_open_id'])){
                $user = $ucmember_model->where($user['id'])->setField('wx_open_id',$info['openid']);    
            }elseif( $user['wx_open_id'] != $info['openid']){
                $this->error('登录失败，openid错误',U('index/index'));
                exit;
            }
            $member_model = M('Member');
            $data = array(
                'login'           => array('exp', '`login`+1'),
                'last_login_time' => NOW_TIME,
                'last_login_ip'   => get_client_ip(1),
            );
//            dump(long2ip($data['last_login_ip']));exit;
            $member_model->where(array('uid'=>$user['id']))->data($data)->save();
            unset($data['login']);
            $ucmember_model->where(array('id'=>$user['id']))->data($data)->save();
            /* 记录登录SESSION和COOKIES */
            $auth = array(
                'uid'             => $user['id'],
                'username'        => get_username($user['id']),
                'last_login_time' => $data['last_login_time'],
            );
            
            session('user_auth', $auth);
            session('user_auth_sign', data_auth_sign($auth));
    		unset($data,$auth,$user);
            $this->success('登录成功',U('index/index'));
        }
        exit;
    }
    
    /**
     * 获取插件的配置数组
     */
    final public function getConfig(){
        static $_config = array();
        $name = 'Scanlogin';
        if(isset($_config[$name])){
            return $_config[$name];
        }
        $config =   array();
        $map['name']    =   $name;
        $map['status']  =   1;
        $config  =   M('Addons')->where($map)->getField('config');
        if($config){
            $config   =   json_decode($config, true);
        }else{
        	return false;
        }
        $_config[$name]     =   $config;
        return $config;
    }
    public function getJson($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }
}
