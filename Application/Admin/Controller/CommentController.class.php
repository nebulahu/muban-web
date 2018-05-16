<?php

namespace Admin\Controller;
class CommentController extends AdminController {
	
	public function elist(){
		$map = array();
		if ( isset($_GET['time-start']) ) {
            $map['create_time'][] = array('egt',strtotime(I('time-start')));
        }
        if ( isset($_GET['time-end']) ) {
            $map['create_time'][] = array('elt',24*60*60 + strtotime(I('time-end')));
        }
		$list = $this->lists('Evaluate',$map,'create_time DESC');
		if(!empty($list)){			
			foreach($list as $k=>$v){
				$list[$k]['user'] = D('Member')->getInfo($v['mid']);
				$list[$k]['goods'] = D('Home/Goods')->getGoodsBaseById($v['gid']);
			}
		}
		
		$this->assign('list',$list);
		$this->display();
	}
	
	public function del(){
		/*参数过滤*/
        $ids = I('param.ids');
        if(empty($ids)){
            $this->error('请选择要操作的数据');
        }
		$map = array();
		$map['eid'] = array('in',$ids);
		$flag = M('Evaluate')->where($map)->delete();
		if(!$flag){
			$this->error('删除失败，请重试！');
		}
		$this->success('删除成功！');
	}
	
}
