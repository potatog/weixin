<?php
require_once 'WeChat/OfficialAccounts/pkg/WeChatWeb.class.php';

$ans = WeChatWeb::getWebAccessToken();
echo "<pre>";
print_r($ans);
$ans = WeChatWeb::getWeChatUserInfo($ans["access_token"],$ans["openid"]);
print_r($ans);
