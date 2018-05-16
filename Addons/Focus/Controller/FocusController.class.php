<?php
namespace Addons\Focus\Controller;
use Admin\Controller\AddonsController;
class FocusController extends AddonsController{
	
	public function update(){
            $this->meta_title = '更新焦点图';
            $res = D('Addons://Focus/Focus')->update();
            if(!$res){
                    $this->error(D('Addons://Focus/Focus')->getError());
            }else{
                    if($res['id']){
                            $this->success('更新成功', U('Addons/adminlist/name/Focus'));
                    }else{
                            $this->success('新增成功', U('Addons/adminlist/name/Focus'));
                    }
            }
	}
}
