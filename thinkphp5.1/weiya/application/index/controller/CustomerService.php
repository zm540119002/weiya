<?php
namespace app\index\controller;

use common\component\GatewayClient\Gateway;

class CustomerService extends \common\controller\Base{
    private $user = null;
    public function __construct(){
        parent::__construct();
        //判断是否登录
        $this->user = checkLogin();
    }
    /**绑定用户ID
     */
    public function bindUid(){
        if(request()->isAjax()){
            $postData = input('post.');
            if($this->user){
                // client_id与uid绑定
                Gateway::bindUid($postData['client_id'], $this->user['id']);
            }
            return successMsg('成功！');
        }
    }
    /**发送消息
     */
    public function sendMessage(){
        if(request()->isAjax()){
            $postData = input('post.');
            $msgCreateTime = time();
            $msgId = '';
            if($this->user){//已登录
                if($this->user['id']==$postData['to_user_id']){
                    return errorMsg('不能发给自己！');
                }
                $modelChatMessage = new \common\model\ChatMessage();
                $saveData = [
                    'from_id' => $this->user['id'],
                    'to_id' => $postData['to_user_id'],
                    'content' => $postData['content'],
                    'create_time' => $msgCreateTime,
                ];
                //目的UID在线，标记为已发送
                if(Gateway::isUidOnline($postData['to_user_id'])){
                    $saveData['send_sign'] = 1;
                }
                $res = $modelChatMessage->edit($saveData);
                if($res['status']==0){
                    return errorMsg('保存失败！',$res);
                }
                $msgId = $res['id'];
                if(Gateway::isUidOnline($postData['to_user_id'])){
                    $msg = [
                        'type' => 'msg',
                        'content' => $postData['content'],
                        'from_id' => $this->user['id'],
                        'from_name' => $this->user['name'],
                        'avatar' => $this->user['avatar'],
                        'create_time' => date('Y-m-d H:i',$msgCreateTime),
                        'id' => $msgId,
                    ];
                    Gateway::sendToUid($postData['to_user_id'],json_encode($msg));
                }
            }else{//未登录
                if(Gateway::isUidOnline($postData['to_user_id'])){
                    $msg = [
                        'type' => 'msg',
                        'content' => $postData['content'],
                        'from_id' => $postData['from_client_id'],
                        'from_name' => '游客',
                        'avatar' => '',
                        'create_time' => date('Y-m-d H:i',$msgCreateTime),
                        'id' => $msgId,
                    ];
                    Gateway::sendToUid($postData['to_user_id'],json_encode($msg));
                }
            }
            $postData['who'] = 'me';
            $postData['name'] = $this->user['name'];
            $postData['avatar'] = $this->user['avatar'];
            $postData['create_time'] = $msgCreateTime;
            $postData['id'] = $msgId;
            $this->assign('info',$postData);
            return view('online_service/info_tpl');
        }
    }
    /**设置消息已读
     */
    public function setMessageRead(){
        if(request()->isAjax()){
            $postData = input('post.');
            if($this->user){
                $modelChatMessage = new \common\model\ChatMessage();
                $where =
                    '`status` = 0 and `read` = 0 and id in (' . implode (",",$postData['messageIds']) .
                    ') and from_id = ' . $postData['from_id'] . ' and to_id = ' . $this->user['id'];
                $res = $modelChatMessage->where($where)->setField('read',1);
                if($res==false){
                    return errorMsg('设置已读出错',$modelChatMessage->getError());
                }
                return successMsg('成功！');
            }
        }
    }
    /**客服聊天列表删除
     */
    public function delCustomerMessage(){
        if(request()->isAjax()){
            $postData = input('post.');
            if($this->user){
                $modelChatMessage = new \common\model\ChatMessage();
                $where =
                    '`status` = 0 and id in (' . implode (",",$postData['messageIds']) .
                    ') and ((from_id = ' . $postData['from_id'] . ' and to_id = ' . $this->user['id'] .') ' .
                    'or (from_id = ' . $this->user['id'] . ' and to_id = ' . $postData['from_id'] . '))';
                $res = $modelChatMessage->where($where)->setField('status',2);
                if($res==false){
                    return errorMsg('删除失败！',$modelChatMessage->getError());
                }
                return successMsg('删除成功！');
            }
        }
    }
}