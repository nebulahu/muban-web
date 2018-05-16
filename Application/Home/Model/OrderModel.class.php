<?php

namespace Home\Model;
use Think\Model;

class OrderModel extends Model{
	
	public function bulidSn(){
		$sn = 'sn'.date('YmdHis',time()).mt_rand(10000,99999);
		return $sn;
	}
	
	public function lists($map = array(),$order='create_time DESC',$limit){
		dump($limit);exit;
		$list = M('Order')->order($order)->where($map)->limit($limit)->select();

		return $list;
	}
	public function getCount($map){
		$count = M('Order')->where($map)->count();
		return $count;
	}
	public function getOrderGoods($id){
		$list = M('Order_goods')->where('order_id='.$id)->select();
		return $list;
	}
}
