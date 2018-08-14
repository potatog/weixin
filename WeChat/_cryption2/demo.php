<?php

require_once 'wxBizDataCrypt.php';


$appid = 'wx2868aec295765bff';
$sessionKey = 'tiihtNczf5v6AKRyjwEUhQ==';

$encryptedData="DHahEkY3OKB2QgN5d9kpLBJdw8WMISF+uw2KbTnpndbzvT/KTNN+NB/oIs2xwK5SkyQIGZne/pINMtE3Tper+AxNBTATwXnj90+F1I+W5Ox6GBIMeegg2Xt5wuct9rzonmdHLcMXgPkcoFHGA8dgR8PaAVViTsmsf7Y5VqDI+piaLEnL8YNoUvg/xdTqms++ihBGtSK1kG2iC6L0LiXIeL6aVDP3GGPT3e/DYb2kcQ+k3WTFqYH7rgsiaKXijhIkLWBwZPPGbIMMwRGEC5OyPu2z+X8xQg5vmGdAbGttkiywvCbo9jmrIvXWlSMFPlUlOTDqw4VgGVUDNml12SwH2qyIgifHF5R2fdHggyyu3LG2x5ScRZ5PDtwqsmef6qr/7UQyk+fmt6ihBZx4k/7+qbzdtiAQIcKcfN/XF7eZZw8ctVsAEhqo0rg7/YJigfbnZ3kb70bAQv6oWQiX+96J4w==";

$iv = 'F6wxIePaoUiJBvvjMMkTOw==';

$pc = new WXBizDataCrypt($appid, $sessionKey);
$errCode = $pc->decryptData($encryptedData, $iv, $data );

if ($errCode == 0) {
    print($data . "\n");
} else {
    print($errCode . "\n");
}
