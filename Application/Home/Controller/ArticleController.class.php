<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;

/**
 * 文档模型控制器
 * 文档模型列表和详情
 */
class ArticleController extends HomeController {

    /* 文档模型频道页 */
	public function index(){
		/* 分类信息 */
		$category = $this->category();

		//频道页只显示模板，默认不读取任何内容
		//内容可以通过模板标签自行定制

		/* 模板赋值并渲染模板 */
		$this->assign('category', $category);
		$this->display($category['template_index']);
	}

	/* 文档模型列表页 */
	public function lists($p = 1){
		/* 分类信息 */
		$category = $this->category();
		/* 获取当前分类列表 */
		$Document = D('Document');
		$info = D('Category')->getInfo($category['id']);
		//$fid = D('Category')->getChildrenId($category['id']);
		//dump($fid);exit;
		
		/*if($info == NULL){
			$cid = $category['pid'];
			$pid = $category['id'];
		}else{
			$cid = $category['id'];
			$pid = $info[0]['id'];
		}*/

		$list = $Document->page($p, $category['list_row'])->lists($category['id']);
		if(false === $list){
			$this->error('获取列表数据失败！');
		}

		if (!empty($category['template_lists'])){ //分类已定制模板
			$tmpl = $category['template_lists'];
		} else{
			$tmpl = 'list-news';
		}
		//dump($category);
		//dump($list);
		/* 模板赋值并渲染模板 */
		//$this->assign('fid',$fid);
		$this->assign('category', $category);
		//$this->assign('pid', $pid);
		//$this->assign('fid', $fid);
		$this->assign('cid', $category['id']);
		$this->assign('list', $list);
		$uid = $this->user['uid'];
		$this->assign('uid',$uid);
		//TDK
		$tdk=M('category')->where('id='.$category['id'])->find();
		$description = $tdk['description'];
		
		if(!empty($tdk['meta_title']) && !empty($tdk['keywords'])){
			$title = $tdk['meta_title'];
			$keywords = $tdk['keywords'];
			
		}else{
			$title = $tdk['title'];
			$keywords = $tdk['title'];
		}
		$this->assign('title',$title);
		$this->assign('description',$description);
		$this->assign('keywords',$keywords);
		
		$this->display($tmpl);
	}

	/* 文档模型详情页 */
	public function detail($id = 0, $p = 1){
		/* 标识正确性检测 */
		if(!($id && is_numeric($id))){
			$this->error('文档ID错误！');
		}

		/* 页码检测 */
		$p = intval($p);
		$p = empty($p) ? 1 : $p;

		/* 获取详细信息 */
		$Document = D('Document');
		$info = $Document->detail($id);
		if(!$info){
			$this->error($Document->getError());
		}
		
		/* 分类信息 */
		$category = $this->category($info['category_id']);

		/* 获取模板 */
		if(!empty($info['template'])){//已定制模板
			$tmpl = $info['template'];
		} elseif (!empty($category['template_detail'])){ //分类已定制模板
			$tmpl = $category['template_detail'];
		} else{
			$tmpl = 'show-news';
		}

		/* 更新浏览数 */
		$map = array('id' => $id);
//		$Document->where($map)->setInc('view');
		//产品多图
		$pit = trim($info['duotu'],',');
		$picture = explode(',',$pit);
		$uid = $this->user['uid'];
		$this->assign('uid',$uid);
		/* 模板赋值并渲染模板 */
		$this->assign('cid', $category['id']);
		$this->assign('picture',$picture);
		$this->assign('category', $category);
		$this->assign('info', $info);
		$this->assign('page', $p); //页码
		//TDK
		$tdk = M('document')->where('id='.$id)->find();
		$tdkt = M('category')->where('id='.$tdk['category_id'])->find();
		if(!empty($tdk['meta_title']) && !empty($tdk['description'])){
			$title = $tdk['meta_title'].'-'.$tdkt['title'];
			$description= $tdk['description'];
		}else{
			$title = $tdk['title'].'-'.$tdkt['title'];
			$description = mb_substr($tdk['description'],1,80,'utf-8');
		}	
		$keywords = $tdk['keywords'];
		$this->assign('title',$title);
		$this->assign('description',$description);
		$this->assign('keywords',$keywords);
		$this->display($tmpl);
	}
	//在线留言
	public function message(){
		$action = addons_url('Common://Common/index');
		$uid = $this->user['uid'];
		$info = M('member')->where('uid='.$uid)->find();
		//dump($info);exit;
		$title = '留言';
		$this->assign('title',$title);
		$this->assign('info',$info);
		$this->assign('action',$action);
		$this->display();
	}
	/* 文档分类检测 */
	private function category($id = 0){
		/* 标识正确性检测 */
		$id = $id ? $id : I('get.category', 0);
		if(empty($id)){
			$this->error('没有指定文档分类！');
		}

		/* 获取分类信息 */
		$category = D('Category')->info($id);
		if($category && 1 == $category['status']){
			switch ($category['display']) {
				case 0:
					$this->error('该分类禁止显示！');
					break;
				//TODO: 更多分类显示状态判断
				default:
					return $category;
			}
		} else {
			$this->error('分类不存在或被禁用！');
		}
	}
	
	public function getpic(){
	    $id = I('id');
        $data = M('Document')->field('id,title,category_id,cover_id')
            ->where('id='.$id)->find();
        $thumb = M('Document_album')->where('id='.$id)->find();
        $pic = explode(',',$thumb['duotu']);
        $picname = explode('&',$thumb['pictrue_name']);
        $pic = array_slice($pic,0,-1);
        $album = array();
        foreach ($pic as $k => $v){
            $album[$k]['alt'] = $picname[$k];
            $album[$k]['pid'] = $k;
            $album[$k]['src'] = get_cover($pic[$k],'path');
            $album[$k]['thumb'] = "";
        }
        $pic = array(
            "title" => $data['title'],
            "id" => $data['id'],
            "start" => 0,
            "data" => $album
        );
        return $this->ajaxReturn($pic,"json");
	}
	
	
	 public function getProductList(){
        header('Content-Type:text/html;charset=utf-8');
        $style = I('get.s','');
        $house = I('get.h','');
        $bigarea = (int)I('get.b','');
        $smallarea = (int)I('get.l','');
        $page = I('get.p','1');
        $pos = I('get.pos');
        import('Think.Pageweb');
        $map['b.category_id'] = array('eq',100);
        $map['b.status'] = array('eq',1);
        if($style){
            $map['a.style']  = array('eq',$style);
        }
        if($house){
            $map['a.house_type']  = array('eq',$house);
        }
        if($bigarea){
            $map['a.area']  = array('lt',$bigarea);
        }
        if($smallarea){
            $map['a.area']  = array('gt',$smallarea);
        }
        if($bigarea && $smallarea){
            $map['a.area']  = array('between',array($smallarea,$bigarea));
        }
        if($pos){
            $map['a.position'] = array('egt',$pos);
        }
        $total = count(M('DocumentProduct as a')
            ->join('RIGHT join onethink_document as b ON a.id = b.id')
            ->where($map)->order('a.`id` desc,b.`level` desc')->select());
        $__PAGE__ = new \Think\Pageweb($total,8);
        $_page = $__PAGE__->show();
        $productlist = M('DocumentProduct as a')
            ->join('RIGHT join onethink_document as b ON a.id = b.id')
            ->where($map)->order('a.`id` desc,b.`level` desc')
            ->page($page,8)
            ->select();
        foreach($productlist as $key => $value){
            $productlist[$key]['cover_img'] = get_cover($value['cover_id'],'path');
        }
        $product = array('productlist'=>$productlist,'_page'=>$_page);
        $this->ajaxReturn($product);

    }


    public function searchList(){

        $keyword = I('post.keyword');
        $document = D('Document');
        $where = ' model_id = 4 and title like "%'.$keyword.'%"';
        $list = $this->listss($document,$where,' create_time desc ');
        $catestr= D('Category')->getChildrenId(96);
        $__PAGE__ = new \Think\Pageweb(get_list_count( $catestr), 15);
//        $__PAGE__->show();
        //$brand = M('product_attribute')->where("status = 1 and pid = 0")->limit(8)->select();
        //$this->assign('brand',$brand);
        $this->assign('__PAGE__',$__PAGE__);
        $this->assign('productlist',$list);
        $this->assign('keyword',"搜素'".$keyword."'");
        $this->display('list-product');
    }

    protected function listss ($model,$where=array(),$order='',$field=true){
        $options    =   array();
        $REQUEST    =   (array)I('request.');
        if(is_string($model)){
            $model  =   M($model);
        }
        $OPT        =   new \ReflectionProperty($model,'options');
        $OPT->setAccessible(true);

        $pk         =   $model->getPk();
        if($order===null){
            //order置空
        }else if ( isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']),array('desc','asc')) ) {
            $options['order'] = '`'.$REQUEST['_field'].'` '.$REQUEST['_order'];
        }elseif( $order==='' && empty($options['order']) && !empty($pk) ){
            $options['order'] = $pk.' desc';
        }elseif($order){
            $options['order'] = $order;
        }
        unset($REQUEST['_order'],$REQUEST['_field']);

        if(empty($where)){
            $where  =   array('status'=>array('egt',0));
        }
        if( !empty($where)){
            $options['where']   =   $where;
        }
        $options      =   array_merge( (array)$OPT->getValue($model), $options );
        $total        =   $model->where($options['where'])->count();
        if( isset($REQUEST['r']) ){
            $listRows = (int)$REQUEST['r'];
        }else{
            $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        }
        $page = new \Think\Page($total, $listRows, $REQUEST);
        if($total>$listRows){
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $p =$page->show();
        $this->assign('_page', $p? $p: '');
        $this->assign('_total',$total);
        $options['limit'] = $page->firstRow.','.$page->listRows;

        $model->setProperty('options',$options);

        return $model->field($field)->select();
    }

}
