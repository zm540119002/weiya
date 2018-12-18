<?php
namespace app\index_admin\controller;

class Base extends \common\controller\Base{
    public function __construct(){
        parent::__construct();
        $node = new \common\lib\Node();
        $allDisplayMenu = $node->getAllDisplayNode();
        $this->assign('allDisplayMenu',$allDisplayMenu);
    }
}