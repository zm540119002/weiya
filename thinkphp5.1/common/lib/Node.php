<?php
namespace common\lib;

class Node
{
    // 所有菜单
    private $_allNode = [];
    // 所有可显示菜单
    private $_allDisplayNode = [];

    public function __construct(){
        $this->_setAllNode();
    }

    /**获取所有菜单
     */
    public function getAllNode(){
        return $this->_allNode;
    }

    /**获取所有可显示菜单
     */
    public function getAllDisplayNode(){
        $this->_allDisplayNode = $this->_filterNoDisplayNode($this->_allNode);
        return $this->_allDisplayNode;
    }

    /**设置所有菜单
     */
    private function _setAllNode(){
        $this->_allNode = array_merge($this->_allNode,!empty(config('all_node.menu'))?config('all_node.menu'):[]);
        $this->_allNode = array_merge($this->_allNode,!empty(config('module_node.menu'))?config('module_node')[0]:[]);
    }
    
    /**过滤不显示菜单
     */
    private function _filterNoDisplayNode($node){
        if(is_array($node) && !empty($node)){
            foreach ($node as &$value){
                foreach ($value['sub_menu'] as $key=>$val){
                    if(!$val['display']){
                        unset($value['sub_menu'][$key]);
                    }
                }
            }
        }
        return $node;
    }
}