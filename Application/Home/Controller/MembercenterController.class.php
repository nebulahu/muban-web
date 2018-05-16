<?php

namespace Home\Controller;
/**
 * 会员中心控制器
 */
class MembercenterController extends HomeController {
	
	//会员中心首页
	public function index(){
		$user = $this->user;
		$map = array('uid'=>$user['uid']);
		$info = M('member')->where($map)->find();
		$title = '会员中心';
		$this->assign('title',$title);
		$this->assign('info',$info);
		$this->assign('name',$info['name']);
		$this->assign('id',$info['uid']);
		$this->assign('keywords',$keywords);
		$this->assign('description',$description);
		$this->display();
	}
	//会员资料
	public function memberinfo(){
		$uid = I('get.id','');
		$map = array('uid'=>$uid);
		$info = M('member')->where($map)->find();
		//dump($info);exit;
		$title = '用户资料';
		$this->assign('title',$title);
		$this->assign('info',$info);
		$this->display();
	}
	//修改资料
	public function domemberinfo(){
		$data = I('post.');
		$res = M('member')->save($data);
		if(!$res){
			$msg = array('status'=>0,'message'=>'修改失败！');
			$this->ajaxReturn($msg);exit;
		}
		$msg = array('status'=>1,'message'=>'保存成功！');
		$this->ajaxReturn($msg);exit;
	}
	//我的收藏
	public function mycollection(){
		$title = '我的收藏';
		$uid = $this->user['uid'];
		$map = array('m_id'=>$uid);
		$count = M('favorite')->where($map)->count();
		$page = new \Think\Pagewap($count,10);
		$list = M('favorite')->where($map)->limit($page->firstRow.','.$page->listRows)->select();
		if($list){
			foreach($list as $key=>$val){
				$info = M('document')->field('title,cover_id,comment')->where('id='.$val['goods_id'])->find();
				$list[$key]['title'] = $info['title'];
				$list[$key]['cover_id'] = $info['cover_id'];
				$list[$key]['comment'] = $info['comment'];
			}
		}
		$show = $page->getPages();
		//dump($show);exit;
		$this->assign('page',$show);
		$this->assign('list',$list);
		$this->assign('title',$title);
		$this->assign('keywords',$keywords);
		$this->assign('description',$description);
		$this->display();
	}
	/*//会员订单
	public function memberorder(){
		$list = D('Order')->lists();
		if(!empty($list)){
			
			foreach($list as $k=>$v){
				$list[$k]['status_text'] = order_str($v['status']);
				$glist = array();
				$glist = D('Order')->getOrderGoods($v['order_id']);
				$list[$k]['goods'] = $glist;
			}
		}
		$this->assign('list',$list);
		$this->display();
	}*/
	/*搜索订单结果页
	public function ordersearch(){
		
		$this->display();
	}*/
}