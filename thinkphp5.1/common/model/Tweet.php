<?php
namespace common\model;
use GuzzleHttp\Psr7\Request;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class Tweet extends Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'tweet';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	// 别名
	protected $alias = 't';
	protected $connection = 'db_config_common';
	/**
	 * 新增和修改
	 */
	public function edit($storeId =''){
		$data = input('post.');
		$validate = validate('\common\validate\Tweet');
		if(!$result = $validate->check($data)) {
			return errorMsg($validate->getError());
		}
		return successMsg("成功");
	}
	
}