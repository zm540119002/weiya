<?php
namespace common\model;
use think\Model;
use think\Db;
/**
 * 基础模型器
 */

class Brand extends Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'brand';
	// 设置主键
	protected $pk = 'id';
	// 别名
	protected $alias = 'b';
//	// 设置当前模型的数据库连接
	protected $connection = 'db_config_common';

	/**
	 * 编辑 新增和修改
	 */
	public function edit($factoryId = ''){
		$data = input('post.');
		$validate = new \common\validate\Brand();
		if(!$result = $validate ->check($data)) {
			return errorMsg($validate->getError());
		}
		$data['brand_img'] = moveImgFromTemp(config('upload_dir.factory_brand'),basename($data['brand_img']));
		$data['certificate'] = moveImgFromTemp(config('upload_dir.factory_brand'),basename($data['certificate']));
		$data['authorization'] = moveImgFromTemp(config('upload_dir.factory_brand'),basename($data['authorization']));
		$data['factory_id'] = $factoryId;
		if(input('?post.brand_id')){//修改
			$data['update_time'] = time();
			$data['auth_status'] = 0;
			$result = $this->allowField(true)->save($data, ['id' => $data['brand_id']]);
		}else{
			$data['create_time'] = time();
			$result = $this->allowField(true)->save($data);
		}
		if(false !== $result){
			return successMsg("成功");
		}else{
			return errorMsg('失败');
		}
	}
}