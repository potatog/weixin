<?php
require_once 'WeChat/Func.class.php';
require_once 'WeChat/OfficialAccounts/pkg/WeChatConfig.class.php';
require_once 'WeChat/OfficialAccounts/pkg/WeChatInfo.class.php';

class WeChatCustomMenu{
    
    /**
     * 向微信post数据，初始化菜单。
     * 初始化菜单项
     * @param unknown $menuAry
     * @return mixed
     */
    public static function initCustomMenu($menuAry)
    {
        $status = true;
        $msg = '';
        $access_token = WeChatInfo::getWeChatAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$access_token}";
        
        $data = Func::aryToJsonStr2($menuAry);
        $ans = Func::request($url, $data);
        $ans = json_decode($ans,true);
        if($ans["errcode"] != 0)$status = false;
        $msg = $ans["errmsg"];
        
        return array(
            "status" => $status,
            "msg" => $msg
        );
    }
}
/**
 * 
initCustomMenu($menuAry);
参数实例：
$menuAry = array(
    "button" => array(
       [
            "name" => "一级菜单1",
            "sub_button" => array(
                [
                    "type" => "click",
                    "name" => "简单单击事件",
                    "key" => "CLICK_1_1",
                ],
                [
                    "type" => "view",
                    "name" => "网页跳转",
                    "url" => "http://www.baidu.com"
                ],
//                 [//需要绑定有小程序才能使用，否则会报错
//                     "type" => "miniprogram",
//                     "name" => "启动小程序",
//                     "url" => "http://www.baidu.com",
//                     "appid" =>"wx286b93c14bbf93aa",
//                     "pagepath"=>"pages/lunar/index"
//                 ],
                [
                    "type" => "click",
                    "name" => "打开三级菜单",
                    "key" => "CLICK_1_1",
                     "sub_button" => []
                ]
            )
        ],
        [
            "name" => "一级菜单2",
            "sub_button" => array(
                [
                    "type" => "scancode_waitmsg",
                    "name" => "扫码带提示",//扫码后数据返回给服务器，然后需要服务器返回信息到微信。
                    "key" => "2_1",
                    "sub_button" => array()
                ],
                [
                    "type" => "scancode_push",
                    "name" => "扫码推事件",//将二维码的文本内容返回给服务器，如果二维码是链接将跳转。
                    "key" => "2_2",
                    "sub_button" => array()
                ]
            )
        ],
        [
            "name" => "一级菜单3",
            "sub_button" => array(
                [
                    "type" => "pic_sysphoto",
                    "name" => "系统拍照发图",
                    "key" => "3_1",
                    "sub_button" => array()
                ],
                [
                    "type" => "pic_photo_or_album",
                    "name" => "拍照或从相册发图",
                    "key" => "3_2",
                    "sub_button" => array()
                ],
                [
                    "type" => "pic_weixin",
                    "name" => "微信相册发图",
                    "key" => "3_3",
                    "sub_button" => array()
                ],
                [
                    "type" => "location_select",
                    "name" => "发送位置",//系统会得到坐标，地图缩放倍数，位置名称信息
                    "key" => "3_4"
                ]
            )
        ]
        
        
    )
);


 * 
 */