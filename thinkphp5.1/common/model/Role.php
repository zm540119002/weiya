<?php
namespace common\model;

class Role extends Base{
	// 设置当前模型对应的完整数据表名称
	protected $table = 'role';
	// 设置主键
	protected $pk = 'id';
	// 别名
	protected $alias = 'r';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_common';

	//编辑
	public function edit($data){
		$postData = input('post.');
		$validateUser = new \common\validate\User();
		if(!$validateUser->scene('edit')->check($postData)){
			return errorMsg($validateUser->getError());
		}
		if($postData['id'] && intval($postData['id'])){
			$postData['update_time'] = time();
			$this->isUpdate(true)->save($postData);
		}else{
			unset($postData['id']);
			if(isset($data['id']) && $data['id']){
				$postData['type'] = 2;
			}
			$postData['create_time'] = time();
			$this->save($postData);
		}
		if(!$this->getAttr('id')){
			return errorMsg('失败',$this->getError());
		}
		return successMsg('成功！',array('id'=>$this->getAttr('id')));
	}
}