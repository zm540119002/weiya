<?php
namespace app\index\model;

class Store extends \common\model\Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'store';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_common';
	//表的别名
	protected $alias = 's';
	
}