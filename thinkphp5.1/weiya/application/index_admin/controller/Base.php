<?php
namespace app\index_admin\controller;

class Base extends \common\controller\UserBase{
    public function __construct(){
        parent::__construct();
        $this->loginUrl = 'ucenter/UserCenter/login_admin';//用户中心URL
        $this->indexUrl = 'index_admin/Index/index';//用户中心URL
        //菜单
        $node = new \common\lib\Node();
        $allDisplayMenu = $node->getAllDisplayNode();
        $this->assign('allDisplayMenu',$allDisplayMenu);
    }
}