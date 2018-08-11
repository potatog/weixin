<?php
/*
 * 该类用于主动向用户发送服务通知
 */
require_once 'WeChat/OfficialAccounts/pkg/WeChatInfo.class.php';
class WeChatTemplateMsg{
    /**
     * 
     * @param unknown $toUserOpenId 接受者的openid
     * @param unknown $tempalte_id 模板id
     * @param unknown $url 点击消息后跳转的url
     * @param unknown $data 具体消息
      $data = array(
        "模板中设置的变量名" => array(
            "value" => "设置的变量值",
            "color" => "#ff0000"//改文本的颜色
        )
      )
     
     * @param string $miniprogram 小程序调用消息
     * 
      $miniprogram = array(
            "appid" => "xiaochengxuappid12345",
             "pagepath" => "index?foo=bar"
      )
     * 
     */
    public static function postTemplateMsg($toUserOpenId, $tempalte_id, $data, $jumpUrl = false, $miniprogram = false)
    {
        $status = true;
        $msg = "";
        
        $access_token = WeChatInfo::getWeChatAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$access_token}";
        
        $postDataJson = self::toTemplateMsgJsonStr($toUserOpenId, $tempalte_id, $data, $jumpUrl, $miniprogram);
        $ans = Func::request($url, $postDataJson);
        Func::log($url."\r\n".$postDataJson."\r\n".$ans,"OfficialAccounts");
        
        $ans = json_decode($ans,true);
        if($ans["errcode"] != 0 && $ans["errmsg"] != "ok")
        {
            $status = false;
        }
        
        $msg = $ans["errmsg"];
        
        return array(
            "status" => $status,
            "msg" => $msg
        );
    }
    
    public static function toTemplateMsgJsonStr($toUserOpenId, $tempalte_id, $data, $jumpUrl = false, $miniprogram = false)
    {
        $postData = array();
        $postData["touser"] = $toUserOpenId;
        $postData["template_id"] = $tempalte_id;
        
        if($jumpUrl)$postData["url"] = $jumpUrl;
        
        $postData["data"] = $data;
        if($miniprogram)$postData["miniprogram"] = $miniprogram;
        
        $postDataJson = Func::aryToJsonStr($postData);
        return $postDataJson;
    }
    
    /**
     * 删除指定模板id的模板
     * @param unknown $template_id
     * @return mixed
     */
    public static function deleteTemplate($template_id)
    {
        $access_token = WeChatInfo::getWeChatAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/template/del_private_template?access_token={$access_token}";
        $data = '{"template_id":"'.$template_id.'"}';
        
        $ans = Func::request($url, $data);
        Func::log($url."\r\n".$data."\r\n".$ans,"OfficialAccounts");
        
        $ans = json_decode($ans, true);
        return $ans;
    }
    
    /**
     * 获取已添加至帐号下所有模板列表，可在微信公众平台后台中查看模板列表信息
     */
    public static function getTemplateList()
    {
        $access_token = WeChatInfo::getWeChatAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token={$access_token}";
        
        $ans = file_get_contents($url);
        Func::log($ans,"OfficialAccounts");
        $ans = json_decode($ans,true);
        return $ans;
    }
    
    /**
     * 从行业模板库选择模板到帐号后台，获得模板ID的过程可在微信公众平台后台完成。
     * @param unknown $template_id_short
     * @return mixed
     */
    public static function getTemplateID($template_id_short)
    {
        $access_token = WeChatInfo::getWeChatAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token={$access_token}";
        
        $data = '{"template_id_short":"'.$template_id_short.'"}';
        
        $ans = Func::request($url, $data);
        Func::log($data."\r\n".$ans,"OfficialAccounts");
        $ans = json_decode($ans, true);
        return $ans;
    }
    
    
}