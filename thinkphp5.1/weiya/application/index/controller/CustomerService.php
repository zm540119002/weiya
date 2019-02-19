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
            if(!$this->user){
                return errorMsg('绑定对象未登录！');
            }
            // client_id与uid绑定
            Gateway::bindUid($postData['client_id'], $this->user['id']);
            return successMsg('成功！');
        }
    }
    /**发送消息
     */
    public function sendMessage(){
        if(request()->isAjax()){
            $postData = input('post.');
            $postData['content'] = trim($postData['content']);
            $msgCreateTime = time();
            $msgId = 0;
            //返回发送者信息
            $returnData = [];
            $returnData['who'] = 'me';
            $returnData['create_time'] = $msgCreateTime;
            $returnData['read'] = 1;
            $returnData['content'] = $postData['content'];
            if($this->user){//发送者为：注册用户
                $returnData['avatar'] = $this->user['avatar'];
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
                $msg = [
                    'type' => 'msg',
                    'content' => $postData['content'],
                    'from_id' => $this->user['id'],
                    'from_name' => $this->user['name'],
                    'avatar' => $this->user['avatar'],
                    'create_time' => date('Y-m-d H:i',$msgCreateTime),
                    'id' => $msgId,
                ];
                if($postData['to_user_id']){//接收者为：注册用户
                    if(Gateway::isUidOnline($postData['to_user_id'])){//在线
                        $saveData['to_accept'] = 1;
                    }else{//不在线
                    }
                    $res = $modelChatMessage->edit($saveData);
                    if($res['status']==0){
                        return errorMsg('保存失败！',$res);
                    }
                    $msg['id'] = $res['id'];
                    Gateway::sendToUid($postData['to_user_id'],json_encode($msg));
                }elseif($postData['to_client_id']){//接收者为：游客
                    if(Gateway::isOnline($postData['to_client_id'])){//在线
                        Gateway::sendToClient($postData['to_client_id'],json_encode($msg));
                    }else{//不在线
                        return errorMsg('对方未在线！');
                    }
                }else{
                    return errorMsg('缺少参数');
                }
            }else{//发送者为：游客
                $msg = [
                    'type' => 'msg',
                    'content' => $postData['content'],
                    'from_id' => '',
                    'from_name' => '游客',
                    'from_client_id' => $postData['from_client_id'],
                    'create_time' => date('Y-m-d H:i',$msgCreateTime),
                    'id' => $msgId,
                ];
                if($postData['to_user_id']){//接收者为：注册用户
                    Gateway::sendToUid($postData['to_user_id'],json_encode($msg));
                }elseif($postData['to_client_id']){//接收者为：游客
                    if(Gateway::isOnline($postData['to_client_id'])){//在线
                        Gateway::sendToClient($postData['to_client_id'],json_encode($msg));
                    }else{//不在线
                        return errorMsg('对方未在线！');
                    }
                }else{
                    return errorMsg('缺少参数');
                }
            }
            $returnData['id'] = $msgId;
            $this->assign('info',$returnData);
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
                    '`status` = 0 and `read` = 0 and `id` in (' . implode (",",$postData['messageIds']) .
                    ') and from_id = ' . $postData['from_id'] . ' and to_id = ' . $this->user['id'];
                $res = $modelChatMessage->where($where)->setField('read',1);
                if($res===false){
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
                if($res===false){
                    return errorMsg('删除失败！',$modelChatMessage->getError());
                }
                return successMsg('删除成功！');
            }
        }
    }
}