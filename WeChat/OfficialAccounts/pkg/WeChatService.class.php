<?php

/**
 * 此类暂未成功实现
 */
require_once 'WeChat/Func.class.php';
require_once 'WeChat/OfficialAccounts/pkg/WeChatConfig.class.php';
require_once 'WeChat/OfficialAccounts/pkg/WeChatInfo.class.php';

class WeChatService{
    
    public static function insertServiceAccount($kf_account, $nikname, $password, $ACCESS_TOKEN = false)
    {
        if(!$ACCESS_TOKEN)$ACCESS_TOKEN = WeChatInfo::getWeChatAccessToken();
        $url = "https://api.weixin.qq.com/customservice/kfaccount/add?access_token={$ACCESS_TOKEN}";
        
         $data = '{
         "kf_account" : "'.$kf_account.'",
         "nickname" : "'.$nikname.'",
         "password" : "'.$password.'",
        }';
        
        return Func::request($url, $data);
    }
    

}