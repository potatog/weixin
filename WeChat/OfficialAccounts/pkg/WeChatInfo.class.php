<?php
require_once 'WeChat/Func.class.php';
require_once 'WeChat/OfficialAccounts/pkg/WeChatConfig.class.php';
class WeChatInfo
{
    /**
     * 获取微信服务器IP地址
     * @param unknown $ACCESS_TOKEN
     * 如：
     * @return string
     */
    public static function getWeChatServiceIP($ACCESS_TOKEN = false)
    {
        if(!$ACCESS_TOKEN)$ACCESS_TOKEN = self::getWeChatAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token={$ACCESS_TOKEN}";
        $ans = file_get_contents($url);
        Func::log($ans);
        $ans = json_decode($ans,true);
        return $ans;
    }
    
    /**
     * 获得AccessToken
     * @param unknown $appID
     * @param unknown $appsecret
     * {"access_token":"12_ndIlaXawf1o-7yjl0i1NXZ4ZPyTEgNrTQx7GzCMTavvcvyG5hNalPnOsjXksAyDDcJliTpmp15gIGaPR-NbwV-CLvxm-lWbqS1VJRd7OUfAMj_Rimj3LKY-gjyRH5635xNCXXAeEMzKFxow-OYNdAEAKUJ","expires_in":7200}
     */
    public static function getWeChatAccessToken($appID = false, $appsecret = false)
    {
        if(!$appID)$appID = WeChatConfig::$config["appID"];
        if(!$appsecret)$appsecret = WeChatConfig::$config["appsecret"];
        
        //首先判断本地保存的AccessToKen是否过期
        $dir = "Wechat/temp/AccessToken/";
        $path = $dir."AccessToken.txt";
        if(!file_exists($dir))mkdir($dir,null,true);
        if(file_exists($path))
        {
            $content = file_get_contents($path);
            $accessToken = json_decode($content,true);
            if(time() - $accessToken["saveTime"] < ($accessToken["expires_in"] - 600) && strlen($accessToken["access_token"]) > 10)
            {
                return $accessToken["access_token"];
            }
        }
        //通过接口获得新的accessToken
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appID}&secret={$appsecret}";
        $accessTokenJson = file_get_contents($url);
        $accessTokenAry = json_decode($accessTokenJson,true);
        
        $data = array();
        $data["saveTime"] = time();
        $data["access_token"] = $accessTokenAry["access_token"];
        $data["expires_in"] = $accessTokenAry["expires_in"];
        file_put_contents($path, json_encode($data));
        return $data["access_token"];
    }
}