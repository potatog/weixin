<?php
require_once 'WeChat/Func.class.php';
class WeChatAppletLogin{
    public static $AppID = "wx2868aec295765bff";
    public static $AppSecret = "91bf3695e7d6816a174d0245e0a580ad";
    /**
     * 得到登录凭证
     * 通过小程序端传回的code，去获得session_key 和 openid
     * 此处会返回
     * session_key：
     * expires_in：
     * openid：
     * 需要用户授权或者该小程序绑定了某个公众号在相同主体下的微信产品，就会返回 unionid：
     */
    public static function getLoginProof($code,$AppID = false,$AppSecret = false)
    {
        $AppID = $AppID ? $AppID : self::$AppID;
        $AppSecret = $AppSecret ? $AppSecret : self::$AppSecret;
        $requestURL = "https://api.weixin.qq.com/sns/jscode2session?appid={$AppID}&secret={$AppSecret}&js_code={$code}&grant_type=authorization_code";
        $ans = file_get_contents($requestURL);
        Func::log($code."\r\n".$ans,"Applet");
        return json_decode($ans);
    }
    
    /**
     * 
     * @param string $AppID
     * @param string $AppSecret
     * @return mixed
     */
    public static function getAccessToken($AppID = false,$AppSecret = false)
    {
        $AppID = $AppID ? $AppID : self::$AppID;
        $AppSecret = $AppSecret ? $AppSecret : self::$AppSecret;
        $requestURL = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$AppID}&secret={$AppSecret}";
        $ans = file_get_contents($requestURL);
        Func::log($ans,"Applet");
        return json_decode($ans);
    }
}