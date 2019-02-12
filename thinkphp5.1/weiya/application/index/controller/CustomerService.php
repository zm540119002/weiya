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
            $postData['content'] = trim($postData['content']);
            $msgCreateTime = time();
            $msgId = 0;
            //返回发送者信息
            $returnData = [];
            $returnData['who'] = 'me';
            $returnData['create_time'] = $msgCreateTime;
            $returnData['read'] = 1;
            $returnData['content'] = $postData['content'];
            if($this->user){//发送者-已登录
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
                //接收者-已登录：表示接收者已接收消息
                if(Gateway::isUidOnline($postData['to_user_id'])){
                    $saveData['to_accept'] = 1;
                }
                $res = $modelChatMessage->edit($saveData);
                if($res['status']==0){
                    return errorMsg('保存失败！',$res);
                }
                $msgId = $res['id'];
                //接收者-已登录
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
                }else{//接收者-未登录
                }
            }else{//发送者-未登录
                //接收者-已登录
                if(Gateway::isUidOnline($postData['to_user_id'])){
                    $msg = [
                        'type' => 'msg',
                        'content' => $postData['content'],
                        'from_id' => $postData['from_client_id'],
                        'from_name' => '游客',
                        'create_time' => date('Y-m-d H:i',$msgCreateTime),
                        'id' => $msgId,
                    ];
                    Gateway::sendToUid($postData['to_user_id'],json_encode($msg));
                }else{//接收者-未登录
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