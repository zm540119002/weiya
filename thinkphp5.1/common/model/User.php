<?php
namespace common\model;

class User extends Base{
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user';
	// 设置主键
	protected $pk = 'id';
	// 别名
	protected $alias = 'u';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_common';

	//编辑
	public function edit($data,$setSession=false){
		$validateUser = new \common\validate\User();
		if(!$validateUser->scene('edit')->check($data)){
			return errorMsg($validateUser->getError());
		}
		if(isset($data['id']) && intval($data['id'])){
			$data['update_time'] = time();
			$this->isUpdate(true)->save($data);
		}else{
			unset($data['id']);
			$data['create_time'] = time();
			$this->isUpdate(false)->save($data);
			$data['id'] = $this->getAttr('id');
			if(!$this->getAttr('id')){
				return errorMsg('失败',$this->getError());
			}
		}
		if($setSession){
			setSession($data);
		}
		return successMsg('成功！',$data);
	}
}