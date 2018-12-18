<?php
namespace common\model;
use think\Model;
use think\Db;
use think\Route;

/**
 * 基础模型器
 */

class Record extends Base{
	// 设置当前模型对应的完整数据表名称
	protected $table = 'record';
	// 设置主键
	protected $pk = 'id';
	// 别名
	protected $alias = 'r';
	// 设置当前模型的数据库连接
    protected $connection = 'db_config_common';

	/**
	 * 编辑厂商档案 新增和修改
	 * @param string $factoryId
	 * @return array
	 */
	public function edit($factoryId=''){
		$data = input('post.');
		$data['factory_id'] = $factoryId;
		$validate = validate('\common\validate\Record');
		if(!$result = $validate -> check($data)) {
			return errorMsg($validate->getError());
		}
		//把临时文件移动到相应的文件夹下
		if(!empty($data['company_img'])){
			$data['company_img'] = moveImgFromTemp(config('upload_dir.factory_record'),basename($data['company_img']));
		}

		if(!empty($data['logo_img'])){
			$data['logo_img'] = moveImgFromTemp(config('upload_dir.factory_record'),basename($data['logo_img']));
		}

		if(!empty($data['rb_img'])){
			$rse = moveImgsWithDecFromTemp(config('upload_dir.factory_record'),$data['rb_img']);
			$data['rb_img'] = $rse['imgsWithDecNew'];
			$newRbImg = $rse['imgsArray'];
		}
		if(!empty($data['team_activity'])){
			$rse = moveImgsWithDecFromTemp(config('upload_dir.factory_record'),$data['team_activity']);
			$data['team_activity'] = $rse['imgsWithDecNew'];
			$newRbImg = $rse['imgsArray'];
		}

		if(!empty($data['factory_video'])){
			$rse = moveImgsWithDecFromTemp(config('upload_dir.factory_record'),$data['factory_video']);
			$data['factory_video'] = $rse['imgsWithDecNew'];
			$newFactoryVideo = $rse['imgsArray'];
		}
		if(!empty($data['license'])){
			$rse = moveImgsWithDecFromTemp(config('upload_dir.factory_record'),$data['license']);
			$data['license'] = $rse['imgsWithDecNew'];
			$newLicense = $rse['imgsArray'];
		}
		if(!empty($data['glory_img'])){
			$rse = moveImgsWithDecFromTemp(config('upload_dir.factory_record'),$data['glory_img']);
			$data['glory_img'] = $rse['imgsWithDecNew'];
			$newGloryImg = $rse['imgsArray'];
		}

		if(input('?post.record_id') && !input('?post.record_id') == ''){
			//修改
			$config = [
				'where' => [
					['id','=',$data['record_id']],
				],
				'field' => [
					'logo_img','company_img','rb_img','factory_video','license','glory_img'
				],
			];
			$oldRecordInfo = $this -> getInfo($config);
			$data['update_time'] = time();
			$result = $this->allowField(true)->save($data,['id' => $data['record_id'],'factory_id'=>$factoryId]);
			if(false == $result){
				return errorMsg('失败');
			}
			//删除旧图片
			delImgFromPaths($oldRecordInfo['company_img'],$data['company_img']);
			delImgFromPaths($oldRecordInfo['logo_img'],$data['logo_img']);
			if(!empty($oldRecordInfo['rb_img']) && !empty($data['rb_img'])){
				$rbImgWithDec = json_decode($oldRecordInfo['rb_img'],true);
				$oldRbImg = [];
				foreach ($rbImgWithDec as $item){
					$oldRbImg[] = $item['imgSrc'];
				}
				delImgFromPaths($oldRbImg,$newRbImg);
			}
			if(!empty($oldRecordInfo['factory_video']) && !empty($data['factory_video'])) {
				$rbImgWithDec = json_decode($oldRecordInfo['factory_video'],true);
				$oldFactoryVideo = [];
				foreach ($rbImgWithDec as $item){
					$oldFactoryVideo[] = $item['imgSrc'];
				}
				delImgFromPaths($oldFactoryVideo,$newFactoryVideo);
			}
			if(!empty($oldRecordInfo['license']) && !empty($data['license'])){
				$rbImgWithDec = json_decode($oldRecordInfo['license'],true);
				$oldLicense = [];
				foreach ($rbImgWithDec as $item){
					$oldLicense[] = $item['imgSrc'];
				}
				delImgFromPaths($oldLicense,$newLicense);
			}
			if(!empty($oldRecordInfo['glory_img']) && !empty($data['glory_img'])){
				$rbImgWithDec = json_decode($oldRecordInfo['glory_img'],true);
				$oldGloryImg = [];
				foreach ($rbImgWithDec as $item){
					$oldGloryImg[] = $item['imgSrc'];
				}
				delImgFromPaths($oldGloryImg,$newGloryImg);
			}
		}else{
			//增加
			$data['create_time'] = time();
			$result = $this->allowField(true)->save($data);
			if(false == $result){
				return errorMsg('失败');
			}
		}
		if(false !== $result){
			return successMsg("成功");
		}
	}

}