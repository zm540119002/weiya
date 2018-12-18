<?php
namespace app\weiya_customization\model;

class Scene extends \common\model\Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'scene';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_weiya';
	//表的别名
	protected $alias = 's';
	
}