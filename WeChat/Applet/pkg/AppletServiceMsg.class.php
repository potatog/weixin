<?php
require_once 'WeChat/Func.class.php';
require_once 'WeChat/Applet/pkg/AppletConfig.class.php';
require_once 'WeChat/Applet/pkg/AppletAuth.class.php';
require_once 'WeChat/Applet/pkg/AppletStatusCode.class.php';
/**
 * 客服消息接口
 */
class AppletServiceMsg{
    
    public static function receiveAppletRequest($xml = false, $request = false)
    {
        //如果是get请求 表示是url验证 即 在配置微信消息的url时 微信过来的消息
        if(Func::isGET() && !$xml)
        {
            return self::responseAppletUrlValidate();
        }
        //如果不是get请求，表示是微信端发来的具体消息内容
        else
        {
            return self::getAppletMsg($xml, $request);
        }
    }
    
    
    public static function getAppletMsg($xml = false, $request = false)
    {
        $msgXML = $xml ? $xml : file_get_contents("php://input");
        $request = $request ? $request : $_REQUEST;
        
        Func::log($msgXML, "Applet");
        Func::log($request, "Applet");
        $msgAry = Func::xmlToArray($msgXML);
        
        //如果小程序过来的消息是用户发送过来的
        if(isset($msgAry["ToUserName"]))
        {
            return self::getAppletUserMsg($msgXML, $request);
        }
        //如果小程序段的信息是微信服务器的系统消息
        else if(isset($msgAry["AppId"]))
        {
            
        }
    }
    
    /**
     * 解密小程序端用户过来的消息
     * @param unknown $srcRequest
     * 返回解密后的消息数组
    [ToUserName] => 
    [FromUserName] => 
    [CreateTime] => 
    [MsgType] => text
    [Content] => 
    [MsgId] => 
     * 
     */
    public static function getAppletUserMsg($encryptMsg = false, $request = false)
    {
        $appid = AppletConfig::$AppID;
        $token = AppletConfig::$token;
        $encodingAesKey = AppletConfig::$encodingAesKey;
        
        $encryptMsg = $encryptMsg ? $encryptMsg : file_get_contents("php://input");
        $request = $request ? $request : $_REQUEST;
        
        $timeStamp = isset($request["timestamp"]) ? $request["timestamp"] : '';
        $nonce = isset($request["nonce"]) ? $request["nonce"] : '';
        $msg_signature = isset($request["msg_signature"]) ? $request["msg_signature"] : '';
        
        $ans = Func::wechat_decode($encryptMsg, $timeStamp, $nonce , $msg_signature, $appid, $token, $encodingAesKey);
        
        Func::log($ans, "Applet");
        //如果解密成功
        if($ans["status"])
        {
            $userMsg = Func::xmlToArray($ans["data"]);
            //如果是重复的消息
            if(isset($userMsg["MsgId"]) && self::isRepeatMsg($userMsg["MsgId"]))
            {
                return false;
            }
            return $userMsg;
        }
        return false;
    }
    
    /**
     * 响应小程序段的url配置验证请求
     * @param string $json
     */
    public static function responseAppletUrlValidate($json = false)
    {
        if(!Func::isGET())return false;
        if($json)$request = $json;
        else $request = $_REQUEST;

        Func::log($request,"Applet");
        $status = Func::verifyUrlConfig($request,AppletConfig::$token);
        
        if($status)
        {
            $response = $request["echostr"];
            Func::log("验证通过!","Applet");
        }
        else
        {
            $response =  "fail";
            Func::log("验证失败!","Applet");
        }
        echo $response;
        return $response;
        
    }
    
    /**
     * 判断是否是重复的消息
     */
    public static function isRepeatMsg($msgId)
    {
        $status = false;
    
        $tmpMsgTagDir = "WeChat/Applet/temp/MsgId/".date("Ymd")."/";
    
        $tmpMsgTagPath = $tmpMsgTagDir.$msgId.".txt";
        if(!file_exists($tmpMsgTagDir))mkdir($tmpMsgTagDir,null,true);
        //首先判断是否存在该消息文件
        //如果不存在，那么表示该消息不是第一次发送
        if(file_exists($tmpMsgTagPath))
        {
            $status = true;
        }
        //否则新建一个文件，表示
        else
        {
            file_put_contents($tmpMsgTagPath, "");
        }
    
        //删除头一天的普通消息ID标记
        $tmpPreDayMsgTagDir = "WeChat/Applet/temp/MsgId/".date("Ymd",strtotime("-1 day"))."/";
        Func::deleteDirTree($tmpPreDayMsgTagDir,true);
        return $status;
    }
    
    /**
     * 客服接口-发消息
     * 发送消息到小程序用户 用户触发一次 比如打开客服回话 或者用户发送了消息 我们48小时内 最多只能回复5条信息，知道用户下次触发
     * @param unknown $toUser
     * @param unknown $content
     * @param string $msgtype
     */
    public static function postTextServiceMsgToAppletUser($toUser, $content, $msgtype = "text")
    {
        $access_token = AppletAuth::getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";
        
        $data = array(
            "touser" => $toUser,
            "msgtype" => $msgtype,
            "text" => array(
                "content" => $content
            )
        );
        $jsonStr = Func::aryToJsonStr2($data);
        $ans = Func::request($url, $jsonStr);
        Func::log($data, "Applet");
        Func::log($ans, "Applet");
        $ans = json_decode($ans,true);
        if($ans["errcode"] != 0)
        {
            return array(
                "status" => false,
                "msg" => $ans["errmsg"]."(".Status::codeToMsg($ans["errcode"]).")"
            );
        }
        else
        {
            return array(
                "status" => true,
                "msg" => "ok"
            );
        }
    }
    
    /**
     * 发送图片消息给用户
     * @param unknown $toUser
     * @param unknown $media_id
     * @param string $msgtype
     * @return multitype:boolean mixed |multitype:boolean string
     */
    public static function postImageServiceMsgToAppletUser($toUser, $media_id, $msgtype = "image")
    {
        $access_token = AppletAuth::getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";
        
        $data = array(
            "touser" => $toUser,
            "msgtype" => $msgtype,
            "image" => array(
                "media_id" => $media_id
            )
        );
        
        $jsonStr = Func::aryToJsonStr2($data);
        $ans = Func::request($url, $jsonStr);
        Func::log($data, "Applet");
        Func::log($ans, "Applet");
        $ans = json_decode($ans,true);
        if($ans["errcode"] != 0)
        {
            return array(
                "status" => false,
                "msg" => $ans["errmsg"]."(".Status::codeToMsg($ans["errcode"]).")"
            );
        }
        else
        {
            return array(
                "status" => true,
                "msg" => "ok"
            );
        }
    }
  /**
   * 
   * @param unknown $toUser
   * @param unknown $title
   * @param unknown $description 图文链接消息
   * @param unknown $url 图文链接消息被点击后跳转的链接
   * @param string $thumb_url 链接图标
   * @param string $msgtype
   * @return multitype:boolean mixed |multitype:boolean string
   */
    public static function postLinkServiceMsgToAppletUser($toUser, $title, $description, $jump_url = '', $thumb_url = "", $msgtype = "link")
    {
        $access_token = AppletAuth::getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";
        
        $data = array(
            "touser" => $toUser,
            "msgtype" => $msgtype,
            "link" => array(
                "title" => $title,
                "description" => $description,
                "url" => $jump_url,
                "thumb_url" => $thumb_url
            )
        );
        
        $jsonStr = Func::aryToJsonStr2($data);
        $ans = Func::request($url, $jsonStr);
        Func::log($data, "Applet");
        Func::log($ans, "Applet");
        $ans = json_decode($ans,true);
        if($ans["errcode"] != 0)
        {
            return array(
                "status" => false,
                "msg" => $ans["errmsg"]."(".Status::codeToMsg($ans["errcode"]).")"
            );
        }
        else
        {
            return array(
                "status" => true,
                "msg" => "ok"
            );
        }
    }
    
    /**
     * 
     * @param unknown $toUser
     * @param unknown $title
     * @param unknown $pagepath
     * @param unknown $thumb_media_id 小程序的页面路径，跟app.json对齐，支持参数，比如pages/index/index?foo=bar
     * @param string $msgtype
     * @return multitype:boolean mixed |multitype:boolean string 小程序消息卡片的封面， image类型的media_id，通过新增素材接口上传图片文件获得，建议大小为520*416
     */
    public static function postMiniCardServiceMsgToAppletUser($toUser, $title, $pagepath, $thumb_media_id, $msgtype = "miniprogrampage")
    {
        $access_token = AppletAuth::getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";
        
        $data = array(
            "touser" => $toUser,
            "msgtype" => $msgtype,
            "miniprogrampage" => array(
                "title" => $title,
                "pagepath" => $pagepath,
                "thumb_media_id" => $thumb_media_id
            )
        );
        
        $jsonStr = Func::aryToJsonStr2($data);
        $ans = Func::request($url, $jsonStr);
        Func::log($data, "Applet");
        Func::log($ans, "Applet");
        $ans = json_decode($ans,true);
        
        if($ans["errcode"] != 0)
        {
            return array(
                "status" => false,
                "msg" => $ans["errmsg"]."(".Status::codeToMsg($ans["errcode"]).")"
            );
        }
        else
        {
            return array(
                "status" => true,
                "msg" => "ok"
            );
        }
    }
    
    /**
     * 
     * @param unknown $fromuser 开发者微信号
     * @param unknown $touser接收方帐号（收到的OpenID）
     */
    public static function transferCustomerService($fromuser, $touser)
    {
        $CreateTime = time();
        
        echo "<xml>
         <ToUserName><![CDATA[{$touser}]]></ToUserName>
         <FromUserName><![CDATA[{$fromuser}]]></FromUserName>
         <CreateTime>{$CreateTime}</CreateTime>
         <MsgType><![CDATA[transfer_customer_service]]></MsgType>
         </xml>";
    }
    
    
    
} 