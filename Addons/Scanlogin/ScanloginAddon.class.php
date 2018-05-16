<?php

namespace Addons\Scanlogin;
use Common\Controller\Addon;


/**
 * 微信扫码登录插件
 * @author 第一枪
 */

    class ScanloginAddon extends Addon{

        public $info = array(
            'name'=>'Scanlogin',
            'title'=>'微信扫码登录',
            'description'=>'实现微信扫码登录',
            'status'=>1,
            'author'=>'第一枪',
            'version'=>'0.1'
        );

        public function install(){
            return true;
        }

        public function uninstall(){
            return true;
        }

        //实现的Scanlogin钩子方法
        public function Scanlogin($param){
            //https://open.weixin.qq.com/connect/qrconnect?appid=wx5ec89469e1763e0c&redirect_uri=http%3A%2F%2Fservice.d17.cc%2Fyearswxshare.html&response_type=code&scope=snsapi_login&state=STATE#wechat_redirect
            $config = $this->getConfig();
            if(!isset($config['isopen']) || $config['isopen'] == 0){
                return;
            }
            session('opstate',md5(uniqid(rand(), TRUE)));
            $state = session('opstate');
            $url = trim($config['url'],'/');
            $turl = $url.U('home/addons/execute',array('_addons'=>'Scanlogin','_controller'=>'Scanlogin','_action'=>'index','state'=>$state));
            $url = urlencode($url.U('home/addons/execute',array('_addons'=>'Scanlogin','_controller'=>'Scanlogin','_action'=>'index')));
            $wxurl = "https://open.weixin.qq.com/connect/qrconnect?appid={$config['appid']}&redirect_uri={$url}&response_type=code&scope=snsapi_login&state={$state}#wechat_redirect";
            unset($url,$config);
//            $url = urlencode('http://');
            $this->assign('wxurl',$wxurl);
            $this->assign('url',$turl);
            $this->display('widget');

        }



    }
