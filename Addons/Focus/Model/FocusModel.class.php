<?php
namespace Addons\Focus\Model;
use Think\Model;

class FocusModel extends Model{
    /* 自动验证规则 */
    protected $_validate = array(
        array('title', 'require', '标题不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('url', 'require', '链接不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('pic', 'require', '请上传图片', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('groups', 'require', '分组不能为空，默认“default”', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );
    public $model = array(
        'title'=>'焦点图',//新增[title]、编辑[title]、删除[title]的提示
        'template_add'=>'',//自定义新增模板自定义html edit.html 会读取插件根目录的模板
        'template_edit'=>'',//自定义编辑模板html
        'search_key'=>'',// 搜索的字段名，默认是title
        'extend'=>1,
    );

    public $_fields = array(
        'title'=>array(
            'name'=>'title',
            'title'=>'标题',
            'type'=>'string',
            'remark'=>'',
            'is_show'=>1,
            'value'=>'',
        ),
        'url'=>array(
            'name'=>'url',
            'title'=>'链接地址',
            'type'=>'string',
            'remark'=>'如果默认是空的，请手动填写 javascript:(0);',
            'is_show'=>1,
            'value'=>'javascript:(0);',
        ),
        'pic'=>array(
            'name'=>'pic',
            'title'=>'上传图片',
            'type'=>'picture',
            'remark'=>'',
            'is_show'=>1,
            'value'=>'',
        ),
        'sort'=>array(
            'name'=>'sort',
            'title'=>'排序',
            'type'=>'num',
            'remark'=>'',
            'is_show'=>1,
            'value'=>'0',
        ),
        'groups'=>array(
            'name'=>'groups',
            'title'=>'分组',
            'type'=>'string',
            'remark'=>'如果默认是空的，请手动填写默认分组“default”',
            'is_show'=>1,
            'value'=>'default',
        ),
    );
    
    /*  展示数据  */
    public function getList($group = 'default'){
            $map = array();
            if (!empty($group)) {
                $map['groups'] = trim($group);
            }
            $result = $this->where($map)->order('sort asc,id desc')->select();
            foreach($result as $key=>$val){
                $cover = M('picture')->find($val['pic']);
				if($cover['url']!=''){
					$result[$key]['path'] = $cover['url'];
				}else{
					$result[$key]['path'] = $cover['path'];
				}
                
            }
            return $result;
    }
}