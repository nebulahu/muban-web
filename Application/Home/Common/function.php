<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 前台公共库文件
 * 主要定义前台公共函数库
 */

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = 1){
	$verify = new \Think\Verify();
	return $verify->check($code, $id);
}

/**
 * 获取列表总行数
 * @param  string  $category 分类ID
 * @param  integer $status   数据状态
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_list_count($category, $status = 1){
    static $count;
    if(!isset($count[$category])){
        $count[$category] = D('Document')->listCount($category, $status);
    }
    return $count[$category];
}

/**
 * 获取段落总数
 * @param  string $id 文档ID
 * @return integer    段落总数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_part_count($id){
    static $count;
    if(!isset($count[$id])){
        $count[$id] = D('Document')->partCount($id);
    }
    return $count[$id];
}

/**
 * 获取导航URL
 * @param  string $url 导航URL
 * @return string      解析或的url
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_nav_url($url){
    switch ($url) {
        case 'http://' === substr($url, 0, 7):
        case '#' === substr($url, 0, 1):
            break;        
        default:
            $url = U($url);
            break;
    }
    return $url;
}
/**
  * 获取指定分类的子分类
  * @param int $id 分类ID
  * @param int $level 子分类级别(1:一级子分类,2:一至二级子分类,3:一至三级子分类,n:可扩展至N级)
  * @param mixed $extend 用于嵌套循环时附加嵌套前数据(默认false)
  * @reuturn array 对应分类数据
  */
function get_children_by_category($id, $level=1, $extend=false) {
     (!is_numeric($id) || !is_numeric($level)) && exit;

     $value = $extend ? $extend : array();

     switch ($level) {
         case 1:
             $son_ids = D('Category')->getChildrenId($id);
             if (!empty($son_ids)) {
                 if ($extend) {
                     foreach ($extend as $key=>$val) {
                         $extend_son_ids = D('Category')->getChildrenId($val['id']);
                         if (!empty($extend_son_ids)) {
                             $extend_son_ids = strpos($extend_son_ids, ',') ? explode(',', $extend_son_ids) : array($extend_son_ids);
                             $value[$key]['_son'] = D('Category')->getSameLevel($extend_son_ids[0]);
                         }
                     }
                 } else {
                     $son_ids = strpos($son_ids, ',') ? explode(',', $son_ids) : array($son_ids);
                     $value = D('Category')->getSameLevel($son_ids[0]);
                 }
             }
         break;
         case 2:
             $value = get_children_by_category($id, 1);
             $value = get_children_by_category($id, 1, $value);
         break;
         case 3:
             $value = get_children_by_category($id, 2);
             $value = get_children_by_category($id, 1, $value);
         break;
     }
     return $value;
 }
 
 function hasson($id){
      $son = M("Category")->where("display=1 AND status=1 AND pid={$id}")->find();
      if(!empty($son)){
          return 1;
      }
      return 0;
      
 }
 
 function getCommentCount($gid){
	 if(!$gid){
		 return false;
	 }
	 $map = array('gid'=>$gid);
	 $count = M('evaluate')->where($map)->count();
	 return $count;
 }
 function getMemberInfo($mid,$field = '*'){
	 if(!$mid){
		 return false;
	 }
	 $map = array('uid'=>$mid);
	 $info = M('Member')->where($map)->find();
	 if($field != '*'){
		 return $info[$field];
	 }
	 return $info;
 }