<?php
namespace common\model;
use think\Model;
use think\Db;
use think\Route;

/**
 * 基础模型器
 */

class Address extends Base{
	// 设置当前模型对应的完整数据表名称
	protected $table = 'address';
	// 设置主键
	protected $pk = 'id';
	// 别名
	protected $alias = 'a';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_common';


    /**
     * 获取地址列表
     */
    public function getAddressDataList($condition=[],$type='list'){

        $condition['where'][] = ['a.status', '=', 0];

        if($type=='list'){
            return $this ->getList($condition);

        }else{
            return $this->getInfo($condition);
        }
    }
}