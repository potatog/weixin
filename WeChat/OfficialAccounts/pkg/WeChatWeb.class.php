<?php
require_once 'WeChat/OfficialAccounts/pkg/WeChatConfig.class.php';
require_once 'WeChat/Func.class.php';
/**
 * 微信网页授权
 */
 class WeChatWeb{
     
     /**
      * 拼接为用来获取微信权限的URL地址
      * @param unknown $redirect_uri 这个域名需要在微信先配置 【网页账号】->【网页授权获取用户基本信息】 修改
      */
     public static function createAuthWebUrl($redirect_uri, $SCOPE = "snsapi_userinfo")
     {
         $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".WeChatConfig::$config["appID"]."&redirect_uri={$redirect_uri}&response_type=code&scope={$SCOPE}&state=STATE#wechat_redirect";
         return $url;
     }
     
     /**
      * 通过code获得accessToken
      * @param string $code
      * 
      * Array
        (
            [access_token] => 
            [expires_in] => 7200
            [refresh_token] => 
            [openid] => 
            [scope] => snsapi_userinfo
        )
      * 
      */
     public static function getWebAccessToken($code = false)
     {
         if(!$code)$code = $_REQUEST["code"];
         $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".WeChatConfig::$config["appID"]."&secret=".WeChatConfig::$config["appsecret"]."&code={$code}&grant_type=authorization_code";
         $ans = file_get_contents($url);
         Func::log($ans,"OfficialAccounts");
         $ans = json_decode($ans,true);
         return $ans;
     }
     
     /**
      * 刷新accessToken
      * @param unknown $APPID
      * @param unknown $REFRESH_TOKEN
      * @return mixed
      */
     public static function refreshAccessToken($APPID, $REFRESH_TOKEN)
     {
         $url = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid={$APPID}&grant_type=refresh_token&refresh_token={$REFRESH_TOKEN}";
         $ans = file_get_contents($url);
         Func::log($ans,"OfficialAccounts");
         $ans = json_decode($ans, true);
         return $ans;
     }
     
     /**
      * @param unknown $ACCESS_TOKEN
      * @param unknown $OPENID
      * 
      * Array
        (
            [openid] => 
            [nickname] => 
            [sex] => 1
            [language] => zh_CN
            [city] => 
            [province] => 四川
            [country] => 中国
            [headimgurl] => => 
           
            [privilege] => Array
                (
                )
        
        )
      * 
      * 
      */
     public static function getWeChatUserInfo($ACCESS_TOKEN, $OPENID)
     {
         $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$ACCESS_TOKEN}&openid={$OPENID}&lang=zh_CN";
         $ans = file_get_contents($url);
         Func::log($ans,"OfficialAccounts");
         $ans = json_decode($ans, true);
         return $ans;
     }
     
     /**
      * 验证accesstoken
      * @param unknown $ACCESS_TOKEN
      * @param unknown $OPENID
      * @return mixed
      */
     public static function checkAccessToken($ACCESS_TOKEN, $OPENID)
     {
         $url = "https://api.weixin.qq.com/sns/auth?access_token={$ACCESS_TOKEN}&openid={$OPENID}";
         $ans = file_get_contents($url);
         Func::log($ans,"OfficialAccounts");
         $ans = json_decode($ans,true);
         return $ans;
     }
     
 }