<?php
namespace common\model;

class GoodsCategory extends Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'goods_category';
	// 设置主键
	protected $pk = 'id';
	// 别名
	protected $alias = 'gc';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_common';

	//编辑
	public function edit(){
		$postData = input('post.');
		$validateGoodsCategory = new \common\validate\GoodsCategory();
		if(!$validateGoodsCategory->scene('edit')->check($postData)){
			return errorMsg($validateGoodsCategory->getError());
		}
		if($postData['id'] && intval($postData['id'])){
			$postData['update_time'] = time();
			$this->isUpdate(true)->save($postData);
		}else{
			unset($postData['id']);
			$postData['create_time'] = time();
			$this->save($postData);
		}
		if(!$this->getAttr('id')){
			return errorMsg('失败',$this->getError());
		}
		return successMsg('成功！',array('id'=>$this->getAttr('id')));
	}

}