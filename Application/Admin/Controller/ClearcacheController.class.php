<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013  All rights reserved.
// +----------------------------------------------------------------------
// | Author: 码农小兵 <email@devdo.net> <http://www.devdo.net>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Admin\Model\AuthGroupModel;


class ClearcacheController extends AdminController {

    public function index(){

        $cahce_dirs = RUNTIME_PATH;
		$html_dirs = './Application/Html/';
        $this->rmdirr ( $cahce_dirs );
		$this->rmdirr($html_dirs);
        @mkdir ( $cahce_dirs, 0777, true );
		@mkdir ( $html_dirs, 0777, true );
        $this->display ();
    }

    function rmdirr($dirname) {
        if (! file_exists ( $dirname )) {
            return false;
        }
        if (is_file ( $dirname ) || is_link ( $dirname )) {
            return unlink ( $dirname );
        }
        $dir = dir ( $dirname );
        if ($dir) {
            while ( false !== $entry = $dir->read () ) {
                if ($entry == '.' || $entry == '..') {
                    continue;
                }
                $this->rmdirr ( $dirname . DIRECTORY_SEPARATOR . $entry );
            }
        }
        $dir->close ();
        return rmdir ( $dirname );
    }
}
?>
