<?php
require_once 'WeChat/pkg/WeChatInfo.class.php';
require_once "WeChat/Func.class.php";
/**
 * 获取微信关注者的信息
 * @author Administrator
 *
 */
class WeChatFollowUser{
    
    public static function getWeChatFollowUserInfoSingle($openid)
    {
        $ACCESS_TOKEN = WeChatInfo::getWeChatAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$ACCESS_TOKEN}&openid={$openid}&lang=zh_CN";
        $ans = file_get_contents($url);
        Func::log($url."\r\n".$ans,"OfficialAccounts");
        $ans = json_decode($ans,true);
        return $ans;
    }
    
    public static function getWeChatFollowUserInfoBatch($openidList)
    {
        $ACCESS_TOKEN = WeChatInfo::getWeChatAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token={$ACCESS_TOKEN}";
        $data = array();
        foreach ($openidList as $index => $value)
        {
            $item = array();
            $item["openid"] = $value;
            $item["lang"] = "zh_CN";
            $data []= $item;
        }
        
        $data["user_list"] = $data;
        $data = Func::aryToJsonStr2($data);
        $ans = Func::request($url, $data);
        Func::log($url."\r\n".$ans,"OfficialAccounts");
        
        return json_decode($ans, true);
    }
}