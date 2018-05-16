<?php

namespace Addons\Common;
use Common\Controller\Addon;

/**
 * 通用留言插件
 * @author 第一枪
 */

    class CommonAddon extends Addon{

        public $info = array(
            'name'=>'Common',
            'title'=>'留言',
            'description'=>'智网通用留言插件-不涉及会员',
            'status'=>1,
            'author'=>'第一枪',
            'version'=>'0.1'
        );

        public $admin_list = array(
        'model'=>'Common',		//要查的表
  			'fields'=>'*',			//要查的字段
  			'map'=>'',				//查询条件, 如果需要可以再插件类的构造方法里动态重置这个属性
  			'order'=>'id desc',		//排序,
  			'list_grid'=>array( 		//这里定义的是除了id序号外的表格里字段显示的表头名和模型一样支持函数和链接
                'username:姓名',
                'phone:电话',
                //'address:昵称',
                'email:邮箱',
				'time:时间',
				'content:留言内容',
				//'respond:管理员回复',
                'id:操作:[DELETE]|删除',
				//'gid:回复:[RESPOND]|回复'
            ),
        );

        public function install(){
            return true;
        }

        public function uninstall(){
            return true;
        }

        //实现的Common钩子方法
        public function Common($param){
            $action = addons_url('Common://Common/index');
			$ip=get_client_ip();
			$time=date('Y-m-d',time());
			$this->assign('time',$time);
			$this->assign('ip',$ip);
            $this->assign('action',$action);
            $this->display('widget');
        }

    }
