<?php

namespace Admin\Controller;
class OrderController extends AdminController {
	
	public function olists(){
		/* 查询条件初始化*/ 
        $map = array();
        if(isset($_GET['order_sn'])){
			
            $map['order_sn']    =   array('like', '%'.(string)I('order_sn').'%');
        }
		if(isset($_GET['status'])){
			if($_GET['status']!='all'){
				$map['status'] = (int)$_GET['status'];	
			}
        }
		if ( isset($_GET['time-start']) ) {
            $map['create_time'][] = array('egt',strtotime(I('time-start')));
        }
        if ( isset($_GET['time-end']) ) {
            $map['create_time'][] = array('elt',24*60*60 + strtotime(I('time-end')));
        }
		$list = $this->lists('Order',$map,'create_time DESC');
		
		foreach($list as $k=>$v){
			$list[$k]['user'] = D('Member')->getInfo($v['mid']);
			$list[$k]['status_text'] = order_str($v['status']);
			$glist = array();
			$glist = D('Home/Order')->getOrderGoods($v['order_id']);
			$list[$k]['goods'] = $glist;
		}
		
		$this->assign('list', $list);
		$this->display('lists');
	}
	
	public function edit(){
		$map['order_id'] = I('id');
		if(IS_POST){
			$post = I('post.');
			$post['update_time'] = time();
			$flag = M('Order')->where('order_id='.$post['order_id'])->save($post);
			if($flag === false){
              	$this->error('更新失败');
               
            } else {
                $this->success('更新成功');
            }
		}
		$list = $this->lists('Order',$map,'create_time DESC');
		
		foreach($list as $k=>$v){
			$list[$k]['status_text'] = order_str($v['status']);
			$glist = array();
			$glist = D('Home/Order')->getOrderGoods($v['order_id']);
			$list[$k]['goods'] = $glist;
		}
		$this->assign('list', $list);
		$this->display();	
	}
	//业务员
	public function sales(){
		
		$nickname       =   I('nickname');
		$type = I('type');
        $map['status']  =   array('egt',0);
		$map['type_mid'] = session('user_auth')['uid'];
		
        if(is_numeric($nickname)){
            $map['uid'] = $nickname;
			//dump($map);exit;
        }else{
            $map['nickname']    =   array('like', '%'.(string)$nickname.'%');
        }
		if($type != ''){
			$map['type'] = $type;
		}
        $list   = $this->lists('Member', $map);
		foreach($list as $k=>$v){
			$info = '';
			$info = D('UcenterMember')->where(array('id'=>$v['uid']))->field('username,audit')->find();
			$list[$k]['audit'] = $info['audit'];
			$list[$k]['username'] = $info['username'];
		}
        int_to_string($list,array('audit'=>array('0'=>'未审核',1=>'通过',2=>'未通过')));
        $this->assign('_list', $list);
        $this->meta_title = '用户信息';
        $this->display();
	}
	//业务员（客户订单）
	public function solist(){
		$uid = I('uid');
		$user = D('Member')->getInfo($uid);
		if($user['type_mid'] != session('user_auth')['uid']){
			$this->error('该用户不属于您！');
		}
		/* 查询条件初始化*/ 
        $map = array();
		$map['mid'] = $uid;
		//$map = 
        if(isset($_GET['order_sn'])){
			
            $map['order_sn']    =   array('like', '%'.(string)I('order_sn').'%');
        }
		if(isset($_GET['status'])){
			if($_GET['status']!='all'){
				$map['status'] = (int)$_GET['status'];	
			}
        }
		if ( isset($_GET['time-start']) ) {
            $map['create_time'][] = array('egt',strtotime(I('time-start')));
        }
        if ( isset($_GET['time-end']) ) {
            $map['create_time'][] = array('elt',24*60*60 + strtotime(I('time-end')));
        }
		$list = $this->lists('Order',$map,'create_time DESC');
		
		foreach($list as $k=>$v){
			$list[$k]['user'] = D('Member')->getInfo($v['mid']);
			$list[$k]['status_text'] = order_str($v['status']);
			$glist = array();
			$glist = D('Home/Order')->getOrderGoods($v['order_id']);
			$list[$k]['goods'] = $glist;
		}
		
		$this->assign('list', $list);
		$this->display();
	}
	//业务员（全部订单）
	public function saolist(){
		$member_model = M('Member');
		$type_mid = session('user_auth')['uid'];
		$ulist = $member_model->where(array('type_mid'=>$type_mid))->select();
		$map = array();
		
		if(empty($ulist)){
			$this->assign('list',$list);
			$this->display();
			exit;
		}
		foreach($ulist as $v){
			$uid_str  .= $v['uid'].',';
		}
		$uid_str = trim($uid_str,',');
		$map['mid'] = array('in',$uid_str);
		if(isset($_GET['order_sn'])){
			
            $map['order_sn']    =   array('like', '%'.(string)I('order_sn').'%');
        }
		if(isset($_GET['status'])){
			if($_GET['status']!='all'){
				$map['status'] = (int)$_GET['status'];	
			}
        }
		if ( isset($_GET['time-start']) ) {
            $map['create_time'][] = array('egt',strtotime(I('time-start')));
        }
        if ( isset($_GET['time-end']) ) {
            $map['create_time'][] = array('elt',24*60*60 + strtotime(I('time-end')));
        }

		$list = $this->lists('Order',$map,'create_time DESC');
		
		foreach($list as $k=>$v){
			$list[$k]['user'] = D('Member')->getInfo($v['mid']);
			$list[$k]['status_text'] = order_str($v['status']);
			$glist = array();
			$glist = D('Home/Order')->getOrderGoods($v['order_id']);
			$list[$k]['goods'] = $glist;
		}
		
		$this->assign('list', $list);
		$this->display();
	}
}
