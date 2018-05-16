<?php

namespace Home\Controller;
/**
 * 购物车控制器
 */
class OrderController extends HomeController {
	
	//订单列表
	public function lists(){
		$map = array();
		$status = I('status');
		$order_sn = I('order_sn');
		if($status == 2){
			$map['status'] = 2;
		}elseif($status == 1){
			$map['status'] = array('in',array('0,1'));
		}
		if(!empty($order_sn)){
			$map['order_sn'] = array('like',"%{$order_sn}%");	
		}
		$count = D('Order')->getCount($map);
		$page = new \Think\Pagewap($count,10);
	//	$limit = '"'.$page->firstRow.','.$page->listRows.'"';
//		dump($limit);
		$list = M('Order')->order('create_time DESC')->where($map)->limit($page->firstRow.','.$page->listRows)->select();
		if(!empty($list)){
			
			foreach($list as $k=>$v){
				$list[$k]['status_text'] = order_str($v['status']);
				$glist = array();
				$glist = D('Order')->getOrderGoods($v['order_id']);
				$list[$k]['goods'] = $glist;
			}
		}
		$show = $page->getPages();
		$this->assign('page',$show);
		$this->assign('status',$status);
		$this->assign('order_sn',$order_sn);
		$this->assign('list',$list);
		$this->display();
	}
	
	//订单详情
	public function details(){
		$this->display();	
	}
	
	//确认下单
	public function confirm(){
		$cidstr = I('post.cids','');
		if(empty($cidstr)){
			$data=array('status'=>0,'message'=>'参数错误！');
			$this->ajaxReturn($data);exit;
		}
		$user = $this->user;
		$create_time = time();
		
		$cids = trim($cids,',');
		$cids = explode(',',$cidstr);
		$order = $goods = $goods_tmp = array();
		$price = '';	
		foreach($cids as $k=>$v){
			$cart_info = $tmp_info = $tmp_base = array();
			//取出购物车商品
			$cart_info = D('Cart')->getCartByMap(array('id'=>$v,'mid'=>$user['uid']));
			if(empty($cart_info)){
				$data=array('status'=>0,'message'=>'购买失败，错误ID'.$v.'请重试！');
				$this->ajaxReturn($data);exit;
			}
			
			//检测商品是否下架
			$tmp_base = D('Goods')->getGoodsBaseById($cart_info['goods_id']);
			if(!$tmp_base['display']){
				$data=array('status'=>0,'message'=>$tmp_base['title'].'已下架，请重试！');
				$this->ajaxReturn($data);exit;
			}
			
			//检测是否是商品
			$tmp_info = D('Goods')->getGoodsInfoById($cart_info['goods_id']);
			if(empty($tmp_info)){
				$data=array('status'=>0,'message'=>'非法提交，请重试！');
				$this->ajaxReturn($data);exit;
			}
			
			//商品数据
		    $goods_tmp[$k]['goods_id'] = $cart_info['goods_id'];
			$goods_tmp[$k]['mid'] = $user['uid'];
			$goods_tmp[$k]['mp_id'] = $user['mp_id'];
			$goods_tmp[$k]['goods_name'] = $tmp_base['title'];
			$goods_tmp[$k]['price'] = $tmp_info['price'];
			$goods_tmp[$k]['num'] = $cart_info['num'];
			$goods_tmp[$k]['total'] = $tmp_info['price']*$cart_info['num'];
			$goods_tmp[$k]['unit'] = $tmp_info['unit'];
			$goods_tmp[$k]['create_time'] = $create_time;
			$goods_tmp[$k]['cover_id'] = $tmp_base['cover_id'];
			//订单数据
			$order['goodsprice'] += $goods_tmp[$k]['total'];
			
					
		}
		//dump($goods_tmp);exit;
		$order['order_sn'] = D('Order')->bulidSn();
		$order['mid'] = $user['uid'];
		$order['mp_id'] = $user['mp_id'];
		$order['discountprice'] = 0.00;
		$order['dispatchprice'] = 0.00;
		$order['price'] = $order['goodsprice']-$order['discountprice']+$order['dispatchprice'];
		$order['create_time'] = $create_time;
		$order['status'] = 0;
		$order['is_admin'] = $user['is_admin']?$user['is_admin']:0;
		$oid = D('Order')->data($order)->add();
		if(!$oid){
			$data=array('status'=>0,'message'=>'订单生成失败，请重试！');
		    $this->ajaxReturn($data);exit;
		}
		foreach($goods_tmp as $v){
			$v['order_id'] = $oid;
			D('Order_goods')->data($v)->add();
			$goods[] = $v;
		}
	
		unset($goods_tmp,$order);
		
		D('Cart')->remove($cidstr);
		$data=array('status'=>0,'message'=>'订单商品生成功！');
		$this->ajaxReturn($data);exit;
	}
	
	
	
}
