<?php
require_once 'WeChat/Func.class.php';
require_once "WeChat/Applet/pkg/AppletConfig.class.php";
require_once 'WeChat/Applet/pkg/AppletAuth.class.php';
class AppletTemplateMsg{
    
    /**
     * 获取小程序模板库标题列表
     * @param number $offset
     * @param number $count
     * @return mixed
     * 返回列表
     array(4) {
  ["errcode"]=>
  int(0)
  ["errmsg"]=>
  string(2) "ok"
  ["list"]=>
  array(20) {
    [0]=>
    array(2) {
      ["id"]=>
      string(6) "AT0002"
      ["title"]=>
      string(18) "购买成功通知"
    }
    [1]=>
    array(2) {
      ["id"]=>
      string(6) "AT0003"
      ["title"]=>
      string(18) "购买失败通知"
    }
    [2]=>
    array(2) {
      ["id"]=>
      string(6) "AT0004"
      ["title"]=>
      string(12) "交易提醒"
    }。。。。。
     */
    public static function getTemplateList($offset = 0, $count = 20)
    {
        $access_token = AppletAuth::getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/wxopen/template/library/list?access_token={$access_token}";
        
        $data = array();
        $data["offset"] = $offset;
        $data["count"] = $count;
        $data = json_encode($data);
        $ans = Func::request($url, $data);
        Func::log($data."\r\n".$ans, "Applet");
        $data = json_decode($ans,true);
        return $data;
    }
    
    /**
     * 获取模板库某个模板标题下关键词库
     * @param unknown $templateID
     * @return mixed
     * array(5) {
  ["errcode"]=>
  int(0)
  ["errmsg"]=>
  string(2) "ok"
  ["id"]=>
  string(6) "AT0005"
  ["title"]=>
  string(18) "付款成功通知"
  ["keyword_list"]=>
  array(100) {
    [0]=>
    array(3) {
      ["keyword_id"]=>
      int(1)
      ["name"]=>
      string(12) "物品名称"
      ["example"]=>
      string(6) "果汁"
    }
    [1]=>
    array(3) {
      ["keyword_id"]=>
      int(2)
      ["name"]=>
      string(12) "付款时间"
      ["example"]=>
      string(15) "2016年9月9日"
    }
    [2]=>
    array(3) {
      ["keyword_id"]=>
     */
    public static function getTemplateKeywords($sysTemplateID)
    {
        $access_token = AppletAuth::getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/wxopen/template/library/get?access_token={$access_token}";
        $data = array(
            "id" => $sysTemplateID
        );
        $data = json_encode($data);
        $ans = Func::request($url, $data);
        Func::log($data."\r\n".$ans, "Applet");
        $data = json_decode($ans,true);
        return $data;
    }
    
    /**
     * 组合模板并添加至帐号下的个人模板库
     * @param unknown $templateID = "AT0002"
     * @param unknown $keywordIdList = ["","","",]
     */
    public static function groupTemplate($sysTemplateID, $keywordIdList)
    {
        $access_token = AppletAuth::getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/wxopen/template/add?access_token={$access_token}";
        
        $data = array();
        $data["access_token"] = $access_token;
        $data["id"] = $sysTemplateID;
        $data["keyword_id_list"] = $keywordIdList;
        
        $data = json_encode($data);
        
        $ans = Func::request($url, $data);
        Func::log($data."\r\n".$ans, "Applet");
        $data = json_decode($ans,true);
        return $data;
    }
    
    /**
     * 获取帐号下已存在的模板列表
     */
    public static function getHadTemplateList($offset = 0, $count = 20)
    {
        $access_token = AppletAuth::getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/wxopen/template/list?access_token={$access_token}";
        
        $data = array();
        $data["access_token"] = $access_token;
        $data["offset"] = $offset;
        $data["count"] = $count;
        
        $data = json_encode($data);
        
        $ans = Func::request($url, $data);
        Func::log($data."\r\n".$ans, "Applet");
        $data = json_decode($ans,true);
        return $data;
    }
    
    /**
     * 删除帐号下的某个模板
     * @param unknown $templateId
     */
    public static function deleteHadTemplate($templateId)
    {
        $access_token = AppletAuth::getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/wxopen/template/del?access_token={$access_token}";
        
        $data = array();
        $data["access_token"] = $access_token;
        $data["template_id"] = $templateId;
        
        $data = json_encode($data);
        
        $ans = Func::request($url, $data);
        Func::log($data."\r\n".$ans, "Applet");
        $data = json_decode($ans,true);
        return $data;
    }
    
   /**
    * 注意 这个from_id 需要在真机下测试才会有 且 【七天】内有效
    * @param unknown $form_id 表单提交场景下，为 submit 事件带上的 formId；支付场景下，为本次支付的 prepay_id,切只能使用一次。 这个id应该需要保存在自己服务器中某条数据下才行
    * 比如提交表单 新增了订单，那么就把formid存在着订单下，方便之后调用此方法
    * @param unknown $touser 接收者（用户）的 openid
    * @param unknown $template_id 所需下发的模板消息的id
    * @param string $data
    $data = array(
        "keyword1" => array(
            "value" => "keyword1的值"
        ),
        "keyword2" => array(
            "value" => "keyword2的值"
        ),
    );
    * @param string $page 点击模板卡片后的跳转页面，仅限本小程序内的页面。支持带参数,（示例index?foo=bar）。该字段不填则模板无跳转。
    * @param string $emphasis_keyword 模板需要放大的关键词，不填则默认无放大
    * 
    * 成功
    * errorcode => 0,
    * errmsg => "ok"
    * 失败：
    * array(2) { ["errcode"]=> int(41029) ["errmsg"]=> string(53) "form id used count reach limit hint: [M0thsA0366shc1]" } 
    **/
    public static function postTemplateMsgToWechatUser($form_id, $touser, $template_id, $data = false ,$page = false, $emphasis_keyword = false)
    {
        $access_token = AppletAuth::getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token={$access_token}";
        
        $requestData = array();
        $requestData["touser"] = $touser;
        $requestData["form_id"] = $form_id;
        $requestData["template_id"] = $template_id;
        if($data)
        {
            $requestData["data"] = $data;
        }
        if($page)$requestData["page"] = $emphasis_keyword;
        if($emphasis_keyword)$requestData["emphasis_keyword"] = $emphasis_keyword;
        
        $requestData = json_encode($requestData);
        $ans = Func::request($url, $requestData);
        Func::log($requestData."\r\n".$ans, "Applet");
        $data = json_decode($ans,true);
        return $data;
    }
}