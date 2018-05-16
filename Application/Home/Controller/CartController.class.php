<?php

namespace Home\Controller;
/**
 * 购物车控制器
 */
class CartController extends HomeController {
	//购物车详情
	public function index(){
		$user = $this->user;
		$list = D('Cart')->where('mid='.$user['uid'])->order('create_time DESC')->select();
		if(!empty($list)){
			foreach($list as $k=>$v){
				$list[$k]['baseInfo'] = D('Goods')->getGoods($v['goods_id']);
				//$list[$k]['baseInfo']['_info']['total']
			}
			
		}
		$title = '采购清单';
		$this->assign('title',$title);
		//dump($list);exit;
		$this->assign('list',$list);
		$this->display();
	}
	
	//添加到购物车
	public function addCart(){
		$gid = I('post.gid',0,'intval');
		if($gid<1){
			$data=array('status'=>0,'message'=>'添加失败！');
			$this->ajaxReturn($data);exit;
		}
		$goods = D('Goods')->checkGoodsId($gid);
		if($goods == false){
			$data=array('status'=>0,'message'=>'添加商品失败！');
			$this->ajaxReturn($data);exit;
		}
 	    $info = D('Cart')->getCartByMap(array('mid'=>$this->user['uid'],'goods_id'=>$gid));
		$flag = '';
		//如果数据库有数据商品数量增加1，反之新增记录
		if(!empty($info)){
			if($info['num']>=99){
				$data=array('status'=>0,'message'=>'商品数量超过最大购买上限！');
				$this->ajaxReturn($data);exit;
			}
		    $flag = D('Cart')->where(array('id'=>$info['id'],'mid'=>$this->user['uid']))->setInc('num');
		}else{
			$param = array();
			$param['goods_id'] = $gid;
			$param['num'] = 1;
			$param['mid'] = $this->user['uid'];
			$param['mp_id'] = $this->user['mp_id'];
			$param['create_time'] = time();
			$flag = D('Cart')->data($param)->add();
		}
		if(!$flag){
			$data=array('status'=>0,'message'=>'添加购物车失败！');
			$this->ajaxReturn($data);exit;
		}
		$num = D('Cart')->getCount(array('mid'=>$this->user['uid']));
		$data=array('status'=>1,'message'=>'添加购物车成功！','num'=>$num);
		$this->ajaxReturn($data);exit;
		
	}
	//修改购物车商品数量
	public function updateCart(){
		$cid = I('post.cid',0,'intval');
		$num = I('post.num',0,'intval');
		$mid = $this->user['uid'];
		if(!$cid){
			$data=array('status'=>0,'message'=>'参数错误！');
			$this->ajaxReturn($data);exit;
		}
		if($num<1 || $num>99){
			$data=array('status'=>0,'message'=>'商品购买数量错误！');
			$this->ajaxReturn($data);exit;
		}
		
		$flag = D('Cart')->where(array('id'=>$cid,'mid'=>$mid))->data(array('num'=>$num))->save();
		if($flag === false){
			$data=array('status'=>0,'message'=>'修改数量失败！');
			$this->ajaxReturn($data);exit;
		}
		$data=array('status'=>1,'message'=>'修改数量成功！');
	    $this->ajaxReturn($data);exit;
	}
	//购物车删除商品
	public function removeCart(){
		$cid = I('post.cid',0,'intval');
		$mid = $this->user['uid'];
		if(!$cid){
			$data=array('status'=>0,'message'=>'参数错误！');
			$this->ajaxReturn($data);exit;
		}
		$flag = D('Cart')->remove($cid,array('mid'=>$mid));
		if(empty($flag)){
			$data=array('status'=>0,'message'=>'删除失败！');
			$this->ajaxReturn($data);exit;
		}
		$data=array('status'=>1,'message'=>'删除成功！');
		$this->ajaxReturn($data);exit;
	}
	
	
}