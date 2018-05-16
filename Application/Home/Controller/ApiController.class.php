<?php
namespace Home\Controller;
use Think\Controller;
class ApiController extends HomeController {
    public function getBanner(){

        $group = I('group');
        $focus = D('Addons://Focus/Focus')->getList($group);
        $this->ajaxReturn($focus);
    }
    public function getCateList(){
        $Category_model = D('Category');
        $data = $Category_model->getTree();
		//获取栏目的图片
		foreach ($data as $key => $value){
            $data[$key]['pic'] = get_cover($value['icon'],'path');
            if($value['_']){
                foreach($value['_'] as $k => $v){
                    $data[$key]['_'][$k]['pic'] = get_cover($v['icon'],'path');
                }
            }
        }
        $this->ajaxReturn($data);
    }
    public function getArticleList(){


        $map = array();
        $map['page'] = I('post.page',1);
        $map['limit'] = I('post.limit',10);
        $map['cateId'] = I('post.cateId');

        $map['order'] = I('post.order','`id` DESC');
        $map['pos'] = I('post.pos');

        $expand = I('post.expand');

        $data = $this->getArticleListModel($map['cateId'],$map['order'],$map['page'],$map['limit'],$map['pos'],$expand);
        $this->ajaxReturn($data);
    }

    public function getArticleDetail(){
        $Document_model = D('Document');
        $map = array();
        $map['id'] = I('post.id');
        $data = $Document_model->detail($map['id']);
        if($data){
            $data['duotu'] = trim($data['duotu'],',');

            $images = explode(',',$data['duotu']);

            if($images){
                foreach($images as $k=>$v){
                    $data['duotu_arr'][$k] = get_cover($v,'path');
                }

            }
            if($data['cover_id']){
                $data['cover_img'] = get_cover($data['cover_id'],'path');
            }

        }
        $this->ajaxReturn($data);
    }
    //获取公司基本信息
    public function getCompanyBaseInfo(){
        $map = array();
        $map['fields'] = I('post.fields');
        $fields = explode(',',$map['fields']);
        $data = array();
        foreach($fields as $v){
            if($v == 'COMPANY_LOGO' || $v == 'COMPANY_IMGCODE'){
                $imgsrc = get_cover(C("{$v}"),'path');
                $data[$v] = $imgsrc;
            }else{
                $data[$v] = C("{$v}");
            }
        }
        $this->ajaxReturn($data);
    }
    //地图坐标
    public function getMap(){
        /** $Addons_model = D('Addons');
        $info = $Addons_model->where('id=25')->find();
        $data = array();
        if(!empty($info['config'])){
        $tmp = json_decode($info['config'],true);
        $data['longitude'] = $tmp['longitude'];
        $data['latitude'] = $tmp['latitude'];
        }**/
        $tmp = explode(',',C('TEN_MAP'));

        $data['latitude'] = $tmp[0];
        $data['longitude'] = $tmp[1];
        $this->ajaxReturn($data);
    }
	//第二个地图坐标
	public function getSecondMap(){

        $tmp = explode(',',C('TEN_MAP_SECOND'));

        $data['latitude'] = $tmp[0];
        $data['longitude'] = $tmp[1];
        $this->ajaxReturn($data);
    }


    private function getArticleListModel($category, $order = '`id` DESC',$page = 1,$limit = 10,$pos = null,$expand = '' ,$status = 1 ,$field = true){
        $Document_model = D('Document');
        $map = $this->listMap($category,1,$pos);
        $data = array();
        $data['list'] = $Document_model->field($field)->where($map)->page($page, $limit)->order($order)->select();
        $data['listCount'] = $Document_model->where($map)->count('id');
        //dump($data);exit;

        if(!empty($data['list'])){
            foreach($data['list'] as $k=>$v){
                $data['list'][$k]['cover_img'] = get_cover($v['cover_id'],'path');

                if(!empty($expand)){

                    $data['list'][$k]['expand'] = $this->getFieldByAid($v['id'],$v['model_id'],$expand);

                }
            }


        }
        $data['listPageCount'] = 0;
        if($data['listCount']>0){
            $data['listPageCount'] = ceil($data['listCount']/$limit);
        }
        return $data;
    }
    private function listMap($category, $status = 1, $pos = null){
        /* 设置状态 */
        $map = array('status' => $status, 'pid' => 0);

        /* 设置分类 */
        //if(!is_null($category)){
        //    if(is_numeric($category)){
        //        $map['category_id'] = $category;
        //    } else {
        //        $map['category_id'] = array('in', str2arr($category));
        //    }
        //}

        /* 设置分类  做了一级栏目存在二级栏目的处理 */
        if(!is_null($category)){
            if(is_numeric($category)){
                //$categoryids = D('Category')->getChildrenId($category);
                
				$categoryids = D('Category')->getTree($category);
			    $categoryids = $this->getSonId($categoryids);
			   
			    $categoryids = trim($categoryids,',');
			   
				if(is_numeric($categoryids)){
                    $map['category_id'] = $categoryids;
                }else{
                    $map['category_id'] = array('in', str2arr($categoryids));
                }
            } else {
                $cate = str2arr($category);
                $categoryids = '';
                foreach($cate as $k => $v){
                    $categoryids .= D('Category')->getChildrenId($v);
                    $categoryids .= ',';
                }
                $categoryids = rtrim($categoryids, ",");
                $map['category_id'] = array('in', str2arr($categoryids));
            }
        }

        $map['create_time'] = array('lt', NOW_TIME);
        $map['_string']     = 'deadline = 0 OR deadline > ' . NOW_TIME;

        /* 设置推荐位 */
        if(is_numeric($pos)){
            $map[] = "position & {$pos} = {$pos}";
        }

        return $map;
    }

    //留言
    public function message(){
        $data=array();
        if(empty($_POST['content'])){
            $data=array('status'=>0,'message'=>'请填写留言内容');
            $this->ajaxReturn($data);exit;
        }
        //dump($_POST);exit;
        $list=M('common');
        $info['username'] = $_POST['username'];
        $info['phone'] = $_POST['phone'];
        //$info['address'] = $_POST['address'];
        //$info['gid'] = $_POST['gid'];
        //$info['email'] = $_POST['email'];
        $info['ip'] = get_client_ip();
        $info['time'] = date('Y-m-d H:i:s',time());
        $info['content'] = $_POST['content'];
        //dump($info);exit;
        if($list->add($info)){
            $data=array('status'=>1,'message'=>'留言成功');
        }else{
            $data=array('status'=>0,'message'=>'留言失败');
        }
        $this->ajaxReturn($data);
    }

    //获取模型字段
    private function getFieldByAid($id,$mid = 4,$field = '*'){
        if($id<1){
            return false;
        }
        $model = D('model')->where(array("id"=>$mid))->field('name')->find();

        $info = D("document_{$model['name']}")->where(array("id"=>$id))->field("{$field}")->find();
        if(empty($info)){
            return false;
        }

        if($field != '*' ){
            $arr = explode(',',$field);

        }
        return $info;
    }
	
	public function getSonId($categoryids,&$sonid){
		if(empty($categoryids['_'])){

			$sonid .= $categoryids['id'].',';
		}else{

			foreach($categoryids['_'] as $v){

				$this->getSonId($v,$sonid);

			}
			
		}
	    return $sonid;
	}
}