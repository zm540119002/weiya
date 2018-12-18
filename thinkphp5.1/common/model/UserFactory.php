<?php
namespace common\model;

class UserFactory extends Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'user_factory';
	// 设置主键
	protected $pk = 'id';
	// 别名
	protected $alias = 'uf';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_common';

	//设置默认厂商
	public function setDefaultFactory($userId=0,$factoryType){
		if(request()->isAjax()){
			$this->startTrans();//开启事务
			$where = [
				['status','=',0],
				['user_id','=',$userId],
				['factory_type','=',$factoryType],
			];
			$result = $this->where($where)->setField('is_default',0);
			if(false === $result){
				$this->rollback();//回滚事务
				return errorMsg('失败');
			}
			$factoryId = input('post.factoryId');
			if(intval($factoryId)){
				$where[] = ['factory_id','=',$factoryId];
			}
			$result = $this->where($where)->setField('is_default',1);
			if(false === $result){
				$this->rollback();//回滚事务
				return errorMsg('失败');
			}
			$this->commit();//提交事务
			return successMsg("成功");
		}
	}

	//设置用户工厂状态
	public function setStatus($factoryId){
		if(!intval($factoryId)){
			return errorMsg('参数错误');
		}
		$postData = input('post.');
		if(!intval($postData['userId'])){
			return errorMsg('参数错误');
		}
		$where = [
			['user_id', '=', $postData['userId']],
			['factory_id', '=', $factoryId],
			['status', '<>', 2],
		];
		$this->startTrans();//开启事务
		$res = $this->where($where)->setField('status',$postData['status']);
		if(false === $res){
			$this->rollback();//回滚事务
			return errorMsg('失败');
		}
		//设置用户工厂角色状态
		$roleIds = array_unique($postData['roleIds']);
		if(is_array($roleIds) && !empty($roleIds)){
			$modelUserFactoryRole = new \app\factory\model\UserFactoryRole();
			$where = [
				['user_id', '=', $postData['userId']],
				['factory_id', '=', $factoryId],
				['status', '<>', 2],
				['role_id', 'in', $roleIds],
			];
			$res = $modelUserFactoryRole->where($where)->setField('status',$postData['status']);
			if(false === $res){
				$this->rollback();//回滚事务
				return errorMsg('失败');
			}
		}
		$this->commit();//提交事务
		return successMsg('成功');
	}
}