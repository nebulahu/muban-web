<?php

namespace Home\Controller;
/**
 * 收藏控制器
 */
class FavoriteController extends HomeController {
	public function flist(){
		
	}
	
	
	public function changeStatus(){
		$gid = I('post.gid',0,'intval');
		$status = I('post.status',0,'intval'); //1加入收藏 2取消收藏
		$mid = $this->user['uid'];
		if(!$gid || !$status){
			$data=array('status'=>0,'message'=>'参数错误！');
			$this->ajaxReturn($data);exit;
		}
		$map = array();
		$map['m_id'] = $mid;
		$map['goods_id'] = $gid;
		$info = M('Favorite')->where($map)->find();
		if($status == 1){
			if($info){
				$data=array('status'=>0,'message'=>'该商品已被收藏！');
				$this->ajaxReturn($data);exit;
			}else{
				$map['status'] = $status;
				$map['create_time'] = time();
				$res = M('Favorite')->add($map);
				if($res){
					$data=array('status'=>1,'message'=>'已收藏！');
					$this->ajaxReturn($data);exit;
				}else{
					$data=array('status'=>0,'message'=>'收藏失败！');
					$this->ajaxReturn($data);exit;
				}
			}
		}elseif($status == 2){
			if(!$info){
				$data=array('status'=>0,'message'=>'商品出错！');
				$this->ajaxReturn($data);exit;
			}else{
				$del = M('Favorite')->delete($info['fid']);
				if(!$del){
					$data=array('status'=>0,'message'=>'取消收藏失败！');
					$this->ajaxReturn($data);exit;
				}
				$data=array('status'=>1,'message'=>'已取消收藏！');
				$this->ajaxReturn($data);exit;
			}
			
		}
		$data=array('status'=>0,'message'=>'参数错误！');
	    $this->ajaxReturn($data);exit;
	}
}