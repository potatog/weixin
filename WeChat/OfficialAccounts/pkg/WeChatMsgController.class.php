<?php
require_once 'WeChat/Func.class.php';
require_once 'WeChat/OfficialAccounts/pkg/WeChatConfig.class.php';
/**
 * 此类是用于被动接受微信端消息
 * @author Administrator
 *
 */
class WechatMsgController{
    
    function __construct()
    {
        
    }
    
    /**
     * 统一的入口，接收微信过来的消息
     * 如果是验证url配置
     * 成功则返回 echostr，并直接echo echostr
     * 如果失败 则输出字符串  ‘fail’
     * 
     * 如果是微信端发送的消息 则返回消息内容数组
     * status:表示是否存在异常 比如 重发消息之类的
     * msg:异常原因
     * data:微信发送过来的消息 原格式数组化
     */
    function receiveWeChatRequest($msgXML = false)
    {
        //如果是get请求 表示是url验证
        if(Func::isGET() && !$msgXML)
        {
            return self::responseWeChatUrlValidate(WeChatConfig::$config);
        }
        //如果不是get请求，表示是微信端发来的具体消息内容
        else
        {
            return self::getWeChatMsg($msgXML);
        }
    }
    
    /**
     * 接收微信端的消息 
     * 包括普通消息与事件推送 该方法会原样返回微信过来的xml和转换为数组的xml数据。
     * @param string $msgXML
     */
    public static function getWeChatMsg($msgXML = false)
    {
        $status = true;
        $systemMsg = array();
        //首先返回信息给微信服务器 避免消息重复发
        echo "";
        //接收xml消息包
        if(!$msgXML)$msgXML = file_get_contents("php://input");
        Func::log($msgXML,"OfficialAccounts");
        //将xml消息包转化为数组
        $msgAry = Func::xmlToArray($msgXML);
        if(!$msgAry)
        {
            $status = false;
            $systemMsg []= "微信XML消息异常";
            Func::log("微信XML消息异常","OfficialAccounts");
        }
        //判断消息类型
        if($status)
        {
            //如果是普通消息类型
            if(self::isNormalMsg($msgAry["MsgType"]))
            {
                return self::getNormalMsg($msgAry);
            }
            //如果是事件类型推送
            else if(self::isEventMsg($msgAry["MsgType"]))
            {
                return self::getEventMsg($msgAry);
            }
            else 
            {
                $status = false;
                $systemMsg []= "无法判断微信消息类型";
                Func::log("无法判断微信消息类型","OfficialAccounts");
            }
        }
        
        return array(
            "status" => $status,
            "msg" => implode(",", $systemMsg)
        );
    }
    
    /**
     * 
     *  @param string $msgAry 转换为数组的微信推送消息 如果不传 该方法会重新获取
     */
    public static function getEventMsg($msgAry = false)
    {
        $status = true;
        $systemMsg = array();
        
        if(!$msgAry)
        {
            $msgAry = file_get_contents("php://input");
            $msgAry = Func::xmlToArray($msgAry);
        }
        else if(is_string($msgAry))
        {
            $msgAry = Func::xmlToArray($msgAry);
        }
        
        //根据FromUserName + CreateTime判断是否是重发，如果是重发则不予理会
        if(self::isRepeatMsg($msgAry["FromUserName"].$msgAry["CreateTime"]))
        {
            $status = false;
            $systemMsg []= "判断为重复消息 FromUserName + CreateTime";
        }
        return array(
            "status" => $status,
            "msg" => implode(",", $systemMsg),
            "wechatData" => $msgAry
        );
    }
    
    /**
     * @param string $msgAry 转换为数组的微信推送消息 如果不传 该方法会重新获取
     */
    public static function getNormalMsg($msgAry = false)
    {
        $status = true;
        $systemMsg = array();
        
        if(!$msgAry)
        {
            $msgAry = file_get_contents("php://input");
            $msgAry = Func::xmlToArray($msgAry);
        }
        else if(is_string($msgAry))
        {
            $msgAry = Func::xmlToArray($msgAry);
        }
        
        //根据消息id判断是否是重发，如果是重发则不予理会
        if(self::isRepeatMsg($msgAry["MsgId"]))
        {
            $status = false;
            $systemMsg []= "判断为重复消息ID";
        }
        return array(
            "status" => $status,
            "msg" => implode(",", $systemMsg),
            "wechatData" => $msgAry
        );
    }
    
    /**
     * 在配置接口时 填写了 url 与 token值 点击提交后 触发
     * 微信发送的Token验证
     * @param $token 填写在接口配置信息中的Token
     */
    public static function responseWeChatUrlValidate($config)
    {
        if(!Func::isGET())return false;
        $request = $_GET;
        Func::log($request,"OfficialAccounts");
        
        $status = Func::verifyUrlConfig($request,WeChatConfig::$config["token"]);
        
        if($status)
        {
            $response = $request["echostr"];
            Func::log("验证通过!","OfficialAccounts");
        }
        else 
        {
            $response =  "fail";
            Func::log("验证失败!","OfficialAccounts");
        }
        echo $response;
        return $response;
    }
    
    /**
     * 判断普通消息是否是重复的消息
     */
    public static function isRepeatMsg($msgId)
    {
        $status = false;
        
        $tmpMsgTagDir = "WeChat/OfficialAccounts/temp/NormalMsgId/".date("Ymd")."/";
        
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
        $tmpPreDayMsgTagDir = "WeChat/OfficialAccounts/temp/NormalMsgId/".date("Ymd",strtotime("-1 day"))."/";
        Func::deleteDirTree($tmpPreDayMsgTagDir,true);
        return $status;
    }
    

    /**
     * 判断是否是普通消息
     * @param unknown $MsgType
     */
    public static function isNormalMsg($MsgType)
    {
        $MsgTypeList = ["text","image","voice","video","shortvideo","location","link"];
        if(in_array($MsgType, $MsgTypeList))return true;
        return false;
    }
    /**
     * 判断是否是事件消息
     * @param unknown $MsgType
     * @return boolean
     */
    public static function isEventMsg($MsgType)
    {
        $MsgTypeList = ["event"];
        if(in_array($MsgType, $MsgTypeList))return true;
        return false;
    }
};