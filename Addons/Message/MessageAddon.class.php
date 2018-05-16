<?php

namespace Addons\Message;
use Common\Controller\Addon;

/**
 * 模板消息插件
 * @author 第一枪
 */

    class MessageAddon extends Addon{

        public $info = array(
            'name'=>'Message',
            'title'=>'模板消息',
            'description'=>'微信模板消息',
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

        //实现的Message钩子方法
        public function Message($param){

          $config=$this->getConfig();
          $scope=$config['scope'];
          $redirect_url=urlencode(trim($config['url'],"").U('Message/oauth2'));
          $url ="https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=$scope&state=1#wechat_redirect";
          //header("Location:".$url);
          $this->assign('url',$url);
          $this->display('widet');

        }
        //接口函数
        function oauth2(){
          $config=$this->getConfig();
          $appid = $config['appid'];
          $secret = $config['appsecret'];
          $template_id = $config['template_id'];
          $sendurl = trim($config['url'],"");
          $code = $_GET["code"];
          $oauth2Url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code";
          $oauth2 = $this->getJson($oauth2Url);
          $url =  "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
          $token = $this->getjson($url);
          $access_token = $token["access_token"];
          $openid = $oauth2['openid'];
          session('val',$token);
          $time=date('Y-m-d H:i',time());
          $array = array(
            'touser'=>$openid,
            'template_id'=>$template_id,
            'url'=>$sendurl,
            'data'=>array(
              'first'=>array('value'=>'你好，你有一条客户留言','color'=>'#173177'),
              'keyword1'=>array('value'=>'想要向您咨询定制家具方面的问题','color'=>'#173177'),
              'keyword2'=>array('value'=>$time,'color'=>'#173177'),
              'remark'=>array('value'=>'记得上网站后台查看哦亲，祝你生活愉快!','color'=>'#173177')
              )
            );
            var_dump($this->send(urldecode(json_encode($array))));
            //$this->redirect('Index/intoindex');
        }

        function send($data){
          $value=session('val');
          $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$value['access_token'];
          $res = $this->getJson($url,$data);
          return json_decode($res,true);

        }


        function getJson($url,$data = null){
              $ch = curl_init();
              curl_setopt($ch, CURLOPT_URL, $url);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
              if(!empty($data)){
                curl_setopt($ch,CURLOPT_POST,1);
                curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
              }
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
              $output = curl_exec($ch);
              curl_close($ch);
              return json_decode($output, true);
        }

    }
