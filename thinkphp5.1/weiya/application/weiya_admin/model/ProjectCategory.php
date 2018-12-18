<?php
namespace app\admin\model;

class ProjectCategory extends\common\model\Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'project_category';
	// 设置主键
	protected $pk = 'id';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_weiya';
	// 别名
	protected $alias = 'pc';

	//编辑
	public function edit(){
		$postData = input('post.');
		$validate = new \common\validate\ProjectCategory();
		if(!$validate->scene('edit')->check($postData)){
			return errorMsg($validate->getError());
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