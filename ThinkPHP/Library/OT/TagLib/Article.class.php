<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2013 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi.cn@gmail.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace OT\TagLib;
use Think\Template\TagLib;
/**
 * 文档模型标签库
 */
class Article extends TagLib{
    /**
     * 定义标签列表
     * @var array
     */
    protected $tags   =  array(
        'partlist' => array('attr' => 'id,field,page,name', 'close' => 1), //段落列表
        'partpage' => array('attr' => 'id,listrow', 'close' => 0), //段落分页
        'prev'     => array('attr' => 'name,info', 'close' => 1), //获取上一篇文章信息
        'next'     => array('attr' => 'name,info', 'close' => 1), //获取下一篇文章信息
        'page'     => array('attr' => 'cate,listrow,child', 'close' => 0), //列表分页
        'pagewap'     => array('attr' => 'cate,listrow,child', 'close' => 0), //列表分页
        'position' => array('attr' => 'pos,cate,limit,filed,name,pid', 'close' => 1), //获取推荐位列表
        'list'     => array('attr' => 'name,category,child,page,row,field', 'close' => 1), //获取指定分类列表
    );

    public function _list($tag, $content){
        $name   = $tag['name'];
        $cate   = $tag['category'];
        $child  = empty($tag['child']) ? 'false' : $tag['child'];
        $row    = empty($tag['row'])   ? '10' : $tag['row'];
//        $field  = empty($tag['field']) ? 'true' : $tag['field'];
        $field  = empty($tag['field']) ? 'true' : '\''.$tag['field'].'\'';

        $parse  = '<?php ';
        if($child=='true')
        //$parse .= '$__CATE__ = D(\'Category\')->getChildrenId('.$cate.');';
            $parse .= '$categoryids = D(\'Category\')->getChildrenId('.$cate.');';
        if($child=='false')
            $parse .= '$categoryids='.$cate.';';
        $parse .= '$__LIST__ = D(\'Document\')->page(!empty($_GET["p"])?$_GET["p"]:1,'.$row.')->lists(';
        $parse .= '$categoryids, \'`level` DESC,`id` DESC\', 1,';
        $parse .= $field . ');';
        $parse .= ' ?>';
        $parse .= '<volist name="__LIST__" id="'. $name .'">';
        $parse .= $content;
        $parse .= '</volist>';
        return $parse;
    }

    /* 推荐位列表 */
    public function _position($tag, $content){
        $pos    = $tag['pos'];
        $cate   = $tag['cate'];
        $pid    = $tag['pid'];
        $limit  = empty($tag['limit']) ? 'null' : $tag['limit'];
        $field  = empty($tag['field']) ? 'true' : $tag['field'];
        $name   = $tag['name'];
        $parse = '';
        if(!empty($pid)){
            $parse    = '<?php ';
            $parse   .= '$ids = "";$infos = D(\'Category\')->getInfo(';
            $parse   .= $pid . ',"id");foreach($infos as $v):$ids .= $v["id"].","; endforeach;$ids = trim($ids,",");?>'; 
            
        }      
        $parse .= '<?php ';
        $parse .= '$__POSLIST__ = D(\'Document\')->position(';
        $parse .= $pos . ',';
        if(!empty($pid)){
            $parse .= '$ids,';
        }else{
            $parse .= $cate . ',';
        }    
        $parse .= $limit . ',';
        $parse .= $field . ');';
        $parse .= '?>';
        $parse .= '<volist name="__POSLIST__" id="'. $name .'">';
        $parse .= $content;
        $parse .= '</volist>';
        return $parse;
    }

    /* 列表数据分页 */
    public function _page($tag){
        $cate    = $tag['cate'];
        $listrow = $tag['listrow'];
        $child  = empty($tag['child']) ? 'false' : $tag['child'];
        $parse   = '<?php ';
        if($child=='true')
            $parse .= '$catestr= D(\'Category\')->getChildrenId('.$cate.');';
        if($child=='false')
            $parse .= '$catestr='.$cate.';';
        $parse  .= '$__PAGE__ = new \Think\Pageweb(get_list_count( $catestr), ' . $listrow . ');';
        $parse  .= 'echo $__PAGE__->show();';
        $parse  .= ' ?>';
        return $parse;
    }
    
    /* 手机端列表数据分页 */
    public function _pagewap($tag){
        $cate    = $tag['cate'];
        $listrow = $tag['listrow'];
        $child  = empty($tag['child']) ? 'false' : $tag['child'];
        $parse   = '<?php ';
        if($child=='true')
            //$parse .= '$__CATE__ = D(\'Category\')->getChildrenId('.$cate.');';
            $parse .= '$catestr= D(\'Category\')->getChildrenId('.$cate.');';
        if($child=='false')
            $parse .= '$catestr='.$cate.';';
        $parse  .= '$__PAGE__ = new \Think\Pagewap(get_list_count($catestr), ' . $listrow . ');';
        $parse  .= '$page=$__PAGE__->getPages();';
        $parse  .= ' ?>';
        return $parse;
    }

    /* 获取下一篇文章信息 */
    public function _next($tag, $content){
        $name   = $tag['name'];
        $info   = $tag['info'];
        $parse  = '<?php ';
        $parse .= '$' . $name . ' = D(\'Document\')->next($' . $info . ');';
        $parse .= ' ?>';
        $parse .= '<notempty name="' . $name . '">';
        $parse .= $content;
        $parse .= '</notempty>';
        return $parse;
    }

    /* 获取上一篇文章信息 */
    public function _prev($tag, $content){
        $name   = $tag['name'];
        $info   = $tag['info'];
        $parse  = '<?php ';
        $parse .= '$' . $name . ' = D(\'Document\')->prev($' . $info . ');';
        $parse .= ' ?>';
        $parse .= '<notempty name="' . $name . '">';
        $parse .= $content;
        $parse .= '</notempty>';
        return $parse;
    }

    /* 段落数据分页 */
    public function _partpage($tag){
        $id      = $tag['id'];
        if ( isset($tag['listrow']) ) {
            $listrow = $tag['listrow'];
        }else{
            $listrow = 10;
        }
        $parse   = '<?php ';
        $parse  .= '$__PAGE__ = new \Think\Pageweb(get_part_count(' . $id . '), ' . $listrow . ');';
        $parse  .= 'echo $__PAGE__->show();';
        $parse  .= ' ?>';
        return $parse;
    }

    /* 段落列表 */
    public function _partlist($tag, $content){
        $id     = $tag['id'];
        $field  = $tag['field'];
        $name   = $tag['name'];
        if ( isset($tag['listrow']) ) {
            $listrow = $tag['listrow'];
        }else{
            $listrow = 10;
        }
        $parse  = '<?php ';
        $parse .= '$__PARTLIST__ = D(\'Document\')->part(' . $id . ',  !empty($_GET["p"])?$_GET["p"]:1, \'' . $field . '\','. $listrow .');';
        $parse .= ' ?>';
        $parse .= '<?php $page=(!empty($_GET["p"])?$_GET["p"]:1)-1; ?>';
        $parse .= '<volist name="__PARTLIST__" id="'. $name .'">';
        $parse .= $content;
        $parse .= '</volist>';
        return $parse;
    }
}