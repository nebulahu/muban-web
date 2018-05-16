<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 系统配文件
 * 所有系统级别的配置
 */
return array(
	'SITE_URL' => 'http://weilefu.diyiqiang.cn/',
    /* 模块相关配置 */
    'AUTOLOAD_NAMESPACE' => array('Addons' => ONETHINK_ADDON_PATH), //扩展模块列表
    'DEFAULT_MODULE'     => 'Home',
    'MODULE_DENY_LIST'   => array('Common','User','Admin','Install'),
    'MODULE_ALLOW_LIST'  => array('Home','Admin'),

    /* 系统数据加密设置 */
    'DATA_AUTH_KEY' => '7(wBy2pTvzgob.)3Jd~j0#cDUmt9L68*ON=CXGV%', //默认数据加密KEY

    /* 用户相关设置 */
    'USER_MAX_CACHE'     => 1000, //最大缓存用户数
    'USER_ADMINISTRATOR' => 1, //管理员用户ID

    /* URL配置 */
    'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'            => 3, //URL模式
    'VAR_URL_PARAMS'       => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR'    => '/', //PATHINFO URL分割符


    /* 全局过滤配置 */
    'DEFAULT_FILTER' => '', //全局过滤函数
	
    /* 数据库配置 */
   /* 'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => '192.168.0.4', // 服务器地址
    'DB_NAME'   => 'bdsr', // 数据库名
    'DB_USER'   => 'd17ccsite', // 用户名
    'DB_PWD'    => 'd17cc!@#',  // 密码
    'DB_PORT'   => '3360', // 端口
    'DB_PREFIX' => 'onethink_', // 数据库表前缀*/
/* 数据库配置 */
   'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => '127.0.0.1', // 服务器地址
    'DB_NAME'   => 'bdsr', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => 'root',  // 密码
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'onethink_', // 数据库表前缀

    /* 文档模型配置 (文档模型核心配置，请勿更改) */
    'DOCUMENT_MODEL_TYPE' => array(2 => '主题', 1 => '目录', 3 => '段落'),
    'MOBILE_TEMP'=>array(
        //模版文件夹,样式文件夹,样式截图
        1=>array('temp_id'=>1,'html_dir'=>'style_one','style_dir'=>'green','style_img'=>WEBMONKEY.'images/tplpic/t1.jpg','name'=>'绿色'),
        2=>array('temp_id'=>2,'html_dir'=>'style_one','style_dir'=>'orange','style_img'=>WEBMONKEY.'images/tplpic/t2.jpg','name'=>'橙色'),
        3=>array('temp_id'=>3,'html_dir'=>'style_one','style_dir'=>'pink','style_img'=>WEBMONKEY.'images/tplpic/t3.jpg','name'=>'粉色'),
		4=>array('temp_id'=>4,'html_dir'=>'style_one','style_dir'=>'black','style_img'=>WEBMONKEY.'images/tplpic/t4.jpg','name'=>'黑色'),
		5=>array('temp_id'=>5,'html_dir'=>'style_one','style_dir'=>'red','style_img'=>WEBMONKEY.'images/tplpic/t5.jpg','name'=>'红色'),
		6=>array('temp_id'=>6,'html_dir'=>'style_one','style_dir'=>'gray','style_img'=>WEBMONKEY.'images/tplpic/t6.jpg','name'=>'灰色'),
		7=>array('temp_id'=>7,'html_dir'=>'style_one','style_dir'=>'blue','style_img'=>WEBMONKEY.'images/tplpic/t7.jpg','name'=>'蓝色'),
		8=>array('temp_id'=>8,'html_dir'=>'style_one','style_dir'=>'coffee','style_img'=>WEBMONKEY.'images/tplpic/t8.jpg','name'=>'咖啡色')
    ),
	'MOBILE_WMP'=>array(
        //模版文件夹,样式文件夹,样式截图
        1=>array('wmp_id'=>1,'wmp_html'=>'wmp','wmp_style'=>'wcj','wmp_style_img'=>WEBMONKEY.'images/tplpic/wmp.jpg','name'=>'微名片'),
		2=>array('wmp_id'=>2,'wmp_html'=>'newyear','wmp_style'=>'newyear','wmp_style_img'=>WEBMONKEY.'images/tplpic/newyear.jpg','name'=>'元旦'),
		3=>array('wmp_id'=>3,'wmp_html'=>'style_one','wmp_style'=>'green','wmp_style_img'=>WEBMONKEY.'images/tplpic/t3.jpg','name'=>'微名片3'),
        
    ),
);
