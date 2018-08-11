<?php 

// require_once 'WeChat/OfficialAccounts/pkg/WeChatWeb.php';
// $ans = WeChatWeb::createAuthWebUrl("http://umj6jx.natappfree.cc/weixin/page.php");
// echo $ans;

require_once 'WeChat/OfficialAccounts/pkg/WeChatCustomMenu.class.php';

$menuAry = array(
    "button" => array(
       [
            "name" => "一级菜单11",
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
            "name" => "一级菜单22",
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
            "name" => "一级菜单33",
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
// $ans = WeChatCustomMenu::initCustomMenu($menuAry);
// var_dump($ans);

// require_once 'WeChat/OfficialAccounts/pkg/WeChatTemplateMsg.class.php';
// $data = array(
//         "input" => array(
//             "value" => "设置的变量值",
//             "color" => "#ff0000"//改文本的颜色
//         )
//       );
// $ans = WeChatTemplateMsg::postTemplateMsg("oiQhm6I3UHMFTWCMH1aiKE0hwNHQ", "Z7D8q92Z1QOEcfAholnp-M0zj5FeNhUJf1-3jDQcMjU", $data);
// var_dump($ans);
require_once 'WeChat/OfficialAccounts/pkg/WeChatWeb.class.php';
$ans = WeChatWeb::createAuthWebUrl('http://ud92mq.natappfree.cc/weixin/page.php');
echo $ans;


?>