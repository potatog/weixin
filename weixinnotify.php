<?php
echo "";
require_once 'WeChat/OfficialAccounts/pkg/WeChatMsgController.class.php';

$ans = WechatMsgController::receiveWeChatRequest();


require_once 'WeChat/OfficialAccounts/pkg/WeChatBackMsg.class.php';

WeChatBackMsg::backImageMsgToWechatUser($ans["wechatData"]["ToUserName"], $ans["wechatData"]["FromUserName"], $ans["wechatData"]["MediaId"]);