<?php

namespace Home\Controller;
/**
 * 购物车控制器
 */
class CommentController extends HomeController {
	
	function add(){
		$data['gid'] = I('post.gid',0,'intval');
		$data['content'] = I('post.content','');
		$data['star1'] = I('post.star1',0,'intval');
		$data['star2'] = I('post.star2',0,'intval');
		$data['star3'] = I('post.star3',0,'intval');
		$data['mid'] = $this->user['uid'];
		if(empty($data['gid']) || empty($data['content'])){
			$data=array('status'=>0,'message'=>'内容不能为空！');
			$this->ajaxReturn($data);exit;
		}
		if(($data['star1']<0 || $data['star1']>5) || ($data['star2']<0 || $data['star2']>5)||($data['star3']<0 || $data['star3']>5)){
			$data=array('status'=>0,'message'=>'评分不能为空！');
			$this->ajaxReturn($data);exit;
		}
		$data['create_time'] = time();
		$flag = M('evaluate')->data($data)->add();
		if(!$flag){
			$data=array('status'=>0,'message'=>'评论失败，请重试！');
			$this->ajaxReturn($data);exit;
		}
		//计算评分
		$sinfo = M('Goods_score')->where(array('goods_id'=>$data['gid']))->find();

		if(empty($sinfo)){
			M('Goods_score')->data(array('goods_id'=>$data['gid']))->add();
			$sinfo = M('Goods_score')->where(array('goods_id'=>$data['gid']))->find();
		}

		$sdata['star1'] = round(($sinfo['star1']+$data['star1'])/2,2);
		$sdata['star2'] = round(($sinfo['star2']+$data['star2'])/2,2);
		$sdata['star3'] = round(($sinfo['star3']+$data['star3'])/2,2);
		$sdata['score'] = round(($sdata['star1']+$sdata['star2']+$sdata['star3'])/3,2);

		M('Goods_score')->where(array('goods_id'=>$data['gid']))->data($sdata)->save();

		$data=array('status'=>1,'message'=>'评论成功！');
		$this->ajaxReturn($data);exit;
	}
	
	function elist(){
		$gid = I('gid',0,'intval');
		if(!$gid){
			$data=array('status'=>0,'message'=>'参数错误！');
			$this->ajaxReturn($data);exit;
		}
		$info  = D('Goods')->getGoodsInfoById($gid);
		if(empty($info)){
			$data=array('status'=>0,'message'=>'非法操作！');
			$this->ajaxReturn($data);exit;
		}
		unset($info);
		$sinfo = M('Goods_score')->where('goods_id='.$gid)->find();
		if(empty($sinfo)){
			M('Goods_score')->data(array('goods_id'=>$gid))->add();
			$sinfo = M('Goods_score')->where('goods_id='.$gid)->find();
		}
		$map = array();
		$map['gid'] = $gid;
		$count = M('evaluate')->where($map)->count();
		$page = new \Think\Pagewap($count,10);
		$list = M('evaluate')->where($map)->order('create_time DESC')->limit($page->firstRow.','.$page->listRows)->select();
		$show = $page->getPages();
		$this->assign('page',$show);
		$this->assign('title','累计评论');
		$this->assign('list',$list);
		$this->assign('sinfo',$sinfo);
		$this->assign('count',$count);
		$this->display();
	}
	
}
