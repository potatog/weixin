<?php
require_once 'WeChat/Func.class.php';
require_once "WeChat/Applet/pkg/AppletConfig.class.php";
class AppletAuth{
    public static $sessionKeyTempDir = "WeChat/Applet/temp/sessionKey/";
    public static $AccessTokenTempDir = "WeChat/Applet/temp/AccessToken/";
    /**
     * 得到登录凭证
     * 通过小程序端传回的code，去获得session_key 和 openid
     * 此处会返回数组
     * openid：
     * 需要用户授权或者该小程序绑定了某个公众号在相同主体下的微信产品，就会返回 unionid：
     */
    public static function getLoginProof($code,$AppID = false,$AppSecret = false)
    {
        $AppID = $AppID ? $AppID : AppletConfig::$AppID;
        $AppSecret = $AppSecret ? $AppSecret : AppletConfig::$AppSecret;
        $requestURL = "https://api.weixin.qq.com/sns/jscode2session?appid={$AppID}&secret={$AppSecret}&js_code={$code}&grant_type=authorization_code";
        $ans = file_get_contents($requestURL);
        Func::log($code."\r\n".$ans,"Applet");
        $data = json_decode($ans,true);
        if($data["errcode"] != 0)
        {
            $data["openid"] = '';
        }
        //暂存sessionKey
        self::saveSessionKey($data);
        $backdata = array();
        if(isset($data["openid"]))$backdata["openid"] = $data["openid"];
        if(isset($data["unionid"]))$backdata["unionid"] = $data["unionid"];
        if(isset($data["session_key"]))$backdata["session_key"] = $data["session_key"];
        
        return $backdata;
    }
    
    /**
     * 通过openid获得的通话密钥sessionkey
     * 由于sessionkey只会在用户授权时获得，所以需要暂存起来
     * 如果成功 返回 sessionkey,如失败 返回 false
     */
    public static function getSessionKey($openId)
    {
        $dir = self::$sessionKeyTempDir;
        $path = $dir.$openId.".txt";
        if(!file_exists($path))return false;
        $content = file_get_contents($path);
        if(strlen($content) > 0)
        {
            $data = json_decode($content,true);
            return $data["session_key"];
        }
        else return false;
    }
    
    /**
     * 暂存sessionkey
     * @param unknown $openId
     * @param unknown $sessionKey
     * @return boolean
     */
    public static function saveSessionKey($data)
    {
        $dir = self::$sessionKeyTempDir;
        if(!file_exists($dir))mkdir($dir, null, true);
        $path = $dir.$data["openid"].".txt";
        $data["saveTime"] = time();
        file_put_contents($path, json_encode($data));
        return true;
    }
    
    
    /**
     * 获取接口调用凭证 （服务器调用 微信服务器的接口凭证）
     * @param string $AppID
     * @param string $AppSecret
     * @return mixed
     */
    public static function getAccessToken($AppID = false,$AppSecret = false)
    {
        $AppID = $AppID ? $AppID : AppletConfig::$AppID;
        $AppSecret = $AppSecret ? $AppSecret : AppletConfig::$AppSecret;
        
        //首先查看是否存在有效的accesstoken
        $dir = self::$AccessTokenTempDir;
        $path = $dir."AccessToken.txt";
        
        if(file_exists($path))
        {
            $content = file_get_contents($path);
            if(strlen($content) > 0)
            {
                $data = json_decode($content,true);
                
                $expires = $data["expires_in"];
                
                if(time() - $data["saveTime"] <= $data["expires_in"] - 600)
                {
                    return $data["access_token"];
                }
            }
        }
        
        $requestURL = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$AppID}&secret={$AppSecret}";
        $ans = file_get_contents($requestURL);
        Func::log($ans,"Applet");
        $data = json_decode($ans,true);
        $data["saveTime"] = time();
        
        if(!file_exists($dir))mkdir($dir,null,true);
        file_put_contents($path, json_encode($data));
        
        return $data["access_token"];
    }
}