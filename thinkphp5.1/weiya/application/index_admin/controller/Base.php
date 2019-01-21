<?php
namespace app\index_admin\controller;

class Base extends \common\controller\UserBaseAdmin{
    public function __construct(){
        parent::__construct();
        //菜单
        $node = new \common\lib\Node();
        $allDisplayMenu = $node->getAllDisplayNode();
        $this->assign('allDisplayMenu',$allDisplayMenu);
    }
}