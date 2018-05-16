<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------

namespace Addons\EditorForAdmin\Controller;
use Home\Controller\AddonsController;
use Think\Upload;

class UploadController extends AddonsController{

	public $uploader = null;

	/* 上传图片 */
	public function upload(){
		session('upload_error', null);
		if(C('PICTURE_UPLOAD_DRIVER') == 'D17'){
		    $conf_d17 = C('UPLOAD_D17_CONFIG');
		    $files = $_FILES;
		    if($conf_d17['version'] == '5.4'){
		        $return = d17_upload_file($conf_d17['url'],$files['imgFile']['name'],$files['imgFile']['tmp_name'],$files['imgFile']['type']);
		    }elseif($conf_d17['version'] == '5.5'){
		        $return = d17_upload_file_h($conf_d17['url'],$files['imgFile']['name'],$files['imgFile']['tmp_name'],$files['imgFile']['type']);
		    }
		    
		    $result = json_decode($return,true);
		    if($result['flag'] == false){
		        $error = '上传失败';
		        session('upload_error',$error);
		        return false;
		    }
		    if($conf_d17['test'] == 1){
    		    if(empty($result['masterFileList'])){
    		        $error = '未获取到上传数据';
    		        session('upload_error',$error);
    		        return false;
    		    }
                $info['fullpath'] = $conf_d17['pre'].$result['masterFileList'][0]['groupName'].'/'.$result['masterFileList'][0]['fileName'];
		    }else{
    		    if(empty($result['fileList'])){
    		        $error = '未获取到上传数据';
    		        session('upload_error',$error);
    		        return false;
    		    }
    		    $info['fullpath'] = $conf_d17['pre'].$result['fileList'][0]['groupName'].'/'.$result['fileList'][0]['fileName'];
		    }
		    
		    
		}else{
		    /* 上传配置 */
    		$setting = C('EDITOR_UPLOAD');
    
    		/* 调用文件上传组件上传文件 */
    		$this->uploader = new Upload($setting, 'Local');
    		$info   = $this->uploader->upload($_FILES);
    		if($info){
    			$url = C('EDITOR_UPLOAD.rootPath').$info['imgFile']['savepath'].$info['imgFile']['savename'];
    			$url = str_replace('./', '/', $url);
    			$info['fullpath'] = __ROOT__.$url;
    		}
    		session('upload_error', $this->uploader->getError());
		}
		
		return $info;
	}

	//keditor编辑器上传图片处理
	public function ke_upimg(){
		/* 返回标准数据 */
		$return  = array('error' => 0, 'info' => '上传成功', 'data' => '');
		$img = $this->upload();
		/* 记录附件信息 */
		if($img){
			$return['url'] = $img['fullpath'];
			unset($return['info'], $return['data']);
		} else {
			$return['error'] = 1;
			$return['message']   = session('upload_error');
		}

		/* 返回JSON数据 */
		exit(json_encode($return));
	}

	//ueditor编辑器上传图片处理
	public function ue_upimg(){

		$img = $this->upload();
		$return = array();
		$return['url'] = $img['fullpath'];
		$title = htmlspecialchars($_POST['pictitle'], ENT_QUOTES);
		$return['title'] = $title;
		$return['original'] = $img['imgFile']['name'];
		$return['state'] = ($img)? 'SUCCESS' : session('upload_error');
		/* 返回JSON数据 */
		$this->ajaxReturn($return);
	}

}
