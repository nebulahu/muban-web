<?php
namespace Home\Widget;
use Think\Controller;
class CommentWidget extends Controller{
	
	public function elistinc($gid, $limit = 2){
		$map = array('gid'=>$gid);
		$list = M('evaluate')->where($map)->limit($limit)->order('create_time DESC')->select();

		$this->assign('list',$list);
		$this->display('Comment/elistinc');
	}
}