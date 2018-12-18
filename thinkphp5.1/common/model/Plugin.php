<?php
namespace common\model;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class Plugin extends Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'plugin';
	// 设置主键
	protected $pk = 'id';
	// 别名
	protected $alias = 'b';
//	// 设置当前模型的数据库连接
	protected $connection = 'db_config_common';

}