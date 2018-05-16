<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Admin\Model\AuthGroupModel;
use Think\Page;

class MoblieController extends AdminController {

    public function config(){
        $MOBILE_TEMP = C("MOBILE_TEMP");
		$MOBILE_WMP = C("MOBILE_WMP");
        $tree = D('Category')->getTree(0,'id,title,pid');
        $config = M('m_config')->find();
		$this->assign('tree',$tree);
        $this->assign('config',$config);
        $this->assign('MOBILE_WMP',$MOBILE_WMP);
        $this->assign('MOBILE_TEMP',$MOBILE_TEMP);
        $this->display();
    }
    public function save(){
        $data = I('post.');
        if(intval($data['temp_id'])>0){
             $MOBILE_TEMP = C("MOBILE_TEMP");
             $data['html_dir'] = $MOBILE_TEMP[$data['temp_id']]['html_dir'];
             $data['style_dir'] = $MOBILE_TEMP[$data['temp_id']]['style_dir'];
        }
		if(intval($data['wmp_id'])>0){
             $MOBILE_WMP = C("MOBILE_WMP");
             $data['wmp_html'] = $MOBILE_WMP[$data['wmp_id']]['wmp_html'];
             $data['wmp_style'] = $MOBILE_WMP[$data['wmp_id']]['wmp_style'];
        }
        $flag = M('m_config')->data($data)->where('id=1')->save();
        if(!$flag){
            $this->error('修改失败！');
        }
        $this->success('修改成功！');
    }
}