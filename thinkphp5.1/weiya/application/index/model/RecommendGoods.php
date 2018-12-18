<?php
namespace app\index\model;

class RecommendGoods extends\common\model\Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'recommend_goods';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_weiya';
	// 别名
	protected $alias = 'rg';

	
	
}