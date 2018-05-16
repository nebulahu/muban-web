<?php

namespace Addons\SeoCnzz;
use Common\Controller\Addon;

/**
 * SEO统计代码插件
 * @author 无名
 */

    class SeoCnzzAddon extends Addon{

        public $info = array(
            'name'=>'SeoCnzz',
            'title'=>'SEO统计代码',
            'description'=>'外部SEO统计代码',
            'status'=>1,
            'author'=>'第一枪',
            'version'=>'0.1'
        );

        public function install(){
            return true;
        }

        public function uninstall(){
            return true;
        }

        //实现的pageFooter钩子方法
        public function pageFooter($param = array()){

            $this->assign('info',C('SEO_CNZZ'));
            $this->display('widget');
        }

    }