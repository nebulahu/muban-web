<?php

namespace Home\Model;
use Think\Model;

class CartModel extends Model{
	
	//根据条件获取购物车单条记录
	public function getCartByMap($map){
		$info = $this->where($map)->find();
		return $info;
	}
	
	//删除购物车记录
	public function remove($ids,$map = array()){
		if(is_array($ids)){
			$idstr = implode(',',$ids);
			$idstr = trim($idstr,',');
		}else{
			$idstr = $ids;
		}
		
		if(empty($idstr)){
			return false;
		}
		
		$flag = $this->where($map)->delete($idstr); 
		return $flag;
	}
	//计算商品总数
	public function getCount($map = array()){
		$num = $this->where($map)->count();
		return $num;
		
	}
}