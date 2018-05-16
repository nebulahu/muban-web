<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Mobile\Controller;
use OT\DataDictionary;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class IndexController extends HomeController {

	//系统首页
    public function index(){

        $category = D('Category')->getTree();
        $lists    = D('Document')->lists(null);

        $this->assign('category',$category);//栏目
        $this->assign('lists',$lists);//列表
        $this->assign('page',D('Document')->page);//分页
		$this->assign('title',C('WEB_SITE_TITLE'));
        $this->display();
    }
	//微名片
    public function wmp(){

   		set_theme('wmp');
		$config = $this->_temp_config;
		if($config['wmp_html'] == ''){
			echo '您未设置微名片';exit;
		}
//		dump($config);exit;
		$this->assign('config',$config);
        $this->display("wcj/{$config['wmp_html']}");
    }
}