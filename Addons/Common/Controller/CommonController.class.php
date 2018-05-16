<?php

namespace Addons\Common\Controller;
use Home\Controller\AddonsController;

class CommonController extends AddonsController{

    public function index(){
        $in = I('post.');
        $data=array();
        if(empty($in['content'])){
            $data=array('status'=>0,'message'=>'请填写留言内容');
            $this->ajaxReturn($data);exit;
        }
        //dump($in);exit;
        $list=M('common');
        $info['username'] = $in['username'];
        $info['phone'] = $in['phone'];
        $info['address'] = $in['address'];
        //$info['gid'] = $in['gid'];
        $info['email'] = $in['email'];
        $info['ip'] = get_client_ip();
        $info['time'] = date('Y-m-d H:i:s',time());
        $info['content'] = $in['content'];
        //dump($info);exit;
        if($list->add($info)){
            $data=array('status'=>1,'message'=>'留言成功');
        }else{
            $data=array('status'=>0,'message'=>'留言失败');
        }
        $this->ajaxReturn($data);
    }
}
