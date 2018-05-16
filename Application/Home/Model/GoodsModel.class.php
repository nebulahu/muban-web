<?php

namespace Home\Model;
use Think\Model;

class GoodsModel extends Model{
	protected $tableName = 'order';
	
	public function getGoodsBaseById($id){
		$info = M('Document')->where("id={$id}")->find();
		return $info;
	}
	public function getGoodsInfoById($id){
		$info = M('Document_product')->where("id={$id}")->find();
		return $info;
	}
	
	public function checkGoodsId($id){
		$base = $this->getGoodsBaseById($id);
		$info = $this->getGoodsInfoById($id);
		if(empty($base) || empty($info)){
			return false;
		}
		if(!$base['display']){
			return false;
		}
		$base['_info'] = $info;
		return $base;
	}
	public function getGoods($id){
		$base = $this->getGoodsBaseById($id);
		$info = $this->getGoodsInfoById($id);
		$base['_info'] = $info;
		return $base;
	}
}