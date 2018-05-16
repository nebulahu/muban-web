<?php

namespace Addons\Map;
use Common\Controller\Addon;

/**
 * 百度地图插件
 * @author 第一枪
 */

    class MapAddon extends Addon{

        public $info = array(
            'name'=>'Map',
            'title'=>'百度地图',
            'description'=>'百度地图插件',
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
        //实现的Map钩子方法
        public function Map(){
			$type=$this->getConfig();
			$data=array(
				'longitude'=>$type['longitude'],
				'latitude'=>$type['latitude'],
				'name'=>$type['name'],
				'address'=>$type['address'],
				'phone'=>$type['phone'],
				'email'=>$type['email'],
				'size'=>$type['size'],
			);
			//dump($data);exit;
			$this->assign('data',$data);
			$this->display('content');
        }
		//实现的Map钩子方法
        public function mobileMap(){
			$type=$this->getConfig();
			$data=array(
				'longitude'=>$type['longitude'],
				'latitude'=>$type['latitude'],
				'name'=>$type['name'],
				'address'=>$type['address'],
				'phone'=>$type['phone'],
				'email'=>$type['email'],
				'size'=>$type['size'],
			);
			//dump($data);exit;
			$this->assign('data',$data);
			$this->display('mobileMap');
        }

    }
