# 微信小程序开发者服务器端接口说明
--------
## 一、配置系统
    在WeChat/Applet/pkg/AppletConfig.class.php填写配置信息
    $appID=AppID(小程序ID)
    $AppSecret=AppSecret(小程序密钥) 	
    $token=Token(令牌)
    $encodingAesKey=EncodingAESKey(消息加密密钥)
## 二、处理各接口的方法使用说明
### 1.开放接口 登录
    例如如请求地址为 https://www.wg.com/login.php
    那么在login.php中需要
    require_once 'WeChat/Applet/pkg/AppletAuth.class.php';
    $ans = AppletAuth::getLoginProof($_REQUEST["authCode"]);
    其中authCode为小程序端使用wx.login得到的临时凭据，getLoginProof会根据此凭据获得该用户的openid和session_key，即授权登录信息。
    
### 2.接口调用凭证 获取access token
    require_once 'WeChat/Applet/pkg/AppletAuth.class.php';
    $ans = AppletAuth::getAccessToken();
    该方法会使用配置文件中的信息去获取access token
    该方法首先会判断是否存在未过期的access token 存在则使用已存在的，否则重新想微信端获取并保存起来
    access token主要是用于 开发者服务器主动请求微信服务器时的凭据
### 3.模板消息 
    require_once 'WeChat/Applet/pkg/AppletTemplateMsg.class.php';
#### （1）管理
    1.获取小程序模板库标题列表
    $ans = AppletTemplateMsg::getTemplateList();
    
    2.获取模板库某个模板标题下关键词库
    $ans = AppletTemplateMsg::getTemplateKeywords($sysTemplateID)
    $sysTemplateID为模板标题id
    
    3.组合模板并添加至帐号下的个人模板库
    $ans = AppletTemplateMsg::groupTemplate($sysTemplateID, $keywordIdList)
    $templateID  模板标题id 
    $keywordIdList 开发者自行组合好的模板关键词列表
    
    4.获取帐号下已存在的模板列表
    $ans = AppletTemplateMsg::getHadTemplateList()
    
    5.删除帐号下的某个模板
    $ans = AppletTemplateMsg::deleteHadTemplate($templateId)
    $templateID  模板标题id 
#### （2）发送模板消息给小程序用户
    发送条件：
    支付
    当用户在小程序内完成过支付行为，可允许开发者向用户在7天内推送有限条数的模板消息（1次支付可下发3条，多次支付下发条数独立，互相不影响）
    提交表单
    当用户在小程序内发生过提交表单行为且该表单声明为要发模板消息的，开发者需要向用户提供服务时，可允许开发者向用户在7天内推送有限条数的模板消息（1次提交表单可下发1条，多次提交下发条数独立，相互不影响）
    AppletTemplateMsg::postTemplateMsgToWechatUser($form_id, $touser, $template_id, $data = false ,$page = false, $emphasis_keyword = false)
    

    
    
### 4.消息推送【客服消息】
    微信会将消息发送的配置的消息URL中【设置->开发设置->消息推送】暂时只支持xml格式
    在页面中使用 <button open-type="contact" /> 可以显示进入客服会话按钮。
#### （1）接收消息和事件
##### 消息的接收
    require_once 'WeChat/Applet/pkg/AppletServiceMsg.class.php';
    $ans = AppletServiceMsg::receiveAppletRequest();
    receiveAppletRequest会接收微信端的请请求目前分两种 一种是配置URL时的验证消息，另一种是其他消息 如用户发送客服消息，事件消息等等。
    验证消息本方法会处理并返回消息给微信端，正常情况下 配置URL处会配置成功。
    其他消息 如果配置的是安全模式该方法会将解密好的消息以数组的形式返回，且字段与微信的消息文档一致，如果是微信端发送的重复消息，将返回false.
##### 事件的接收


#### （2）发送客服消息
        当用户和小程序客服产生特定动作的交互时（具体动作列表请见下方说明），微信将会把消息数据推送给开发者，开发者可以在一段时间内（目前修改为48小时）调用客服接口
        require_once 'WeChat/Applet/pkg/AppletServiceMsg.class.php';
        客服消息区别于模板消息
        1.发送文本消息
        $ans = AppletServiceMsg::postTextServiceMsgToAppletUser($toUser, $content, $msgtype = "text")
        
        2.发送图片消息
        $ans = AppletServiceMsg::postImageServiceMsgToAppletUser($toUser, $media_id, $msgtype = "image")
        
        3.发送小程序卡片
        $ans = AppletServiceMsg::postMiniCardServiceMsgToAppletUser($toUser, $title, $pagepath, $thumb_media_id, $msgtype = "miniprogrampage")
        
        4.发送图文链接
        $ans = AppletServiceMsg::postLinkServiceMsgToAppletUser($toUser, $title, $description, $jump_url = '', $thumb_url = "", $msgtype = "link")
        
#####    （3）转发用户消息 建立小程序用户与小程序客服直接沟通
        $ans = AppletServiceMsg::transferCustomerService($fromuser, $touser)
        当接收到用户消息后，调用此方法
        $fromuser 为开发者微信号
        $touser 接收方帐号（收到的OpenID）
        
