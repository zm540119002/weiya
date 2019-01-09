<?php
namespace common\model;

class ChatMessage extends Base {
	// 设置当前模型对应的完整数据表名称
	protected $table = 'chat_message';
	// 设置主键
	protected $pk = 'id';
	// 别名
	protected $alias = 'cm';
	// 设置当前模型的数据库连接
	protected $connection = 'db_config_common';

	/**编辑
	 */
	public function edit($data){
		$res = $this->isUpdate(false)->save($data);
		if($res===false){
			return errorMsg('失败',[$this->getError()]);
		}
		return successMsg('成功！',['id'=>$this->getAttr('id')]);
	}
}