<?php
namespace common\controller;

/**供应商验证控制器基类
 */
class FactoryBase extends UserBase{
    protected $factory = null;

    public function __construct(){
        parent::__construct();
        \common\cache\Factory::remove($this->user['id']);
        $factoryList = \common\cache\Factory::get($this->user['id'],config('custom.type'));
        $this->assign('factoryList',$factoryList);
        $factoryCount = count($factoryList);
        if ($factoryCount==0){//没有入住供应商
            $this->success('没有入驻，请入驻',url('Deploy/register'),'no_factory_register',0);
        }elseif($factoryCount==1){//入住一家供应商
            $this->factory = $factoryList[0];
        }elseif($factoryCount>1){//入住多家供应商
            foreach ($factoryList as $factory){
                if($factory['is_default']){//默认供应商
                    $this->factory = $factory;
                    break;
                }
            }
        }
        if(!$this->factory){
            //获取用户-工厂-角色-权限节点
            $nodeList = getUserFactoryRoleNode($this->user['id'],$this->factory['id']);
            $this->assign('nodeIds',array_column($nodeList,'node_id'));
        }
        $this->assign('factory',$this->factory);
    }

    //设置默认供应商
    public function setDefaultFactory(){
        $modelUserFactory = new \common\model\UserFactory();
        return $modelUserFactory->setDefaultFactory($this->user['id'],config('custom.type'));
    }
}