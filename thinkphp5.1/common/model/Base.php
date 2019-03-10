<?php
namespace common\model;

class Base extends \think\Model {
	/**查询多条数据
	 */
	public function getList($config=[]){
		$_config = [
			'field' => [
				'*',
			],
		];
		$_config = array_merge($_config,$config);
		$_model = $this->alias($this->alias);
		foreach ($_config as $key=>$value){
			if(!empty($value)){
				$_model = $_model->$key($value);
			}
		}
		$list = $_model->select();
		return count($list)!=0?$list->toArray():[];
	}

	/**查找一条数据
	 */
	public function getInfo($config=[]){
		$_config = [
			'field' => [
				'*',
			],
		];
		$_config = array_merge($_config,$config);
		$_model = $this->alias($this->alias);
		foreach ($_config as $key=>$value){
			if(!empty($value)){
				$_model = $_model->$key($value);
			}
		}
		$info = $_model->find();
		return $info;
	}

	/**分页查询
	 */
	public function pageQuery($config=[]){
		$_config = [
			'field' => [
				'*',
			],
		];
		$_config = array_merge($_config,$config);
		$_model = $this->alias($this->alias);
		foreach ($_config as $key=>$value){
			if(!empty($value)){
				$_model = $_model->$key($value);
			}
		}
		$pageSize = (isset($_GET['pageSize']) && intval($_GET['pageSize'])) ?
			input('get.pageSize',0,'int') : config('custom.default_page_size');
		return $_model->paginate($pageSize);
	}

	//删除
	public function del($condition=[],$tag=true){
		if(!is_array($condition) || empty($condition)){
			return errorMsg('缺失删除条件');
		}
		$where = [
			['status', '=', 0],
		];
		$where = array_merge($where,$condition);
		if($tag){//标记删除
			$result = $this->where($where)->setField('status',2);
		}else{
			$result = $this->where($where)->delete();
		}
		if($result===false){
			return errorMsg('失败',$this->getError());
		}
		return successMsg('成功');
	}

	/**验证字段唯一性
	 */
	public function checkUnique($fieldName,$config){
		$_config = [
			'field' => [
				$fieldName,
			],
		];
		$_model = $this->alias($this->alias);
		$_config = array_merge($_config,$config);
		foreach ($_config as $key=>$value){
			if(!empty($value)){
				$_model = $_model->$key($value);
			}
		}
		$info = $_model->field($fieldName)->find();
		return $info;
	}

	/**根据手机号码检查正常账号
	 */
	protected function checkUserExistByMobilePhone($mobilePhone){
		if(!isMobile($mobilePhone)){
			return errorMsg('请输入正确的手机号码');
		}
		$modelUser = new \common\model\User();
		$where = [
			['mobile_phone','=',$mobilePhone],
			['status','<>',2],
		];
		$res = $modelUser->where($where)->value('id');
		return $res;
	}

	/**创建账号序列号
	 */
	protected function createUserSN($prefix='msy_'){
		 return $prefix . create_random_str(9,3);
	}
}