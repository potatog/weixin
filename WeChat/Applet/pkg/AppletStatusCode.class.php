<?php
class Status{
    
    public static $status = array(
        "-1" => "系统繁忙，此时请开发者稍候再试",
        "0" => "请求成功",
        "40001" => "获取 access_token 时 AppSecret 错误，或者 access_token 无效。请开发者认真比对 AppSecret 的正确性，或查看是否正在为恰当的小程序调用接口",
        "40002" => "不合法的凭证类型",
        "40003" => "不合法的 OpenID，请开发者确认OpenID否是其他小程序的 OpenID",
        "45015" => "回复时间超过限制",
        "45047" => "客服接口下行条数超过上限",
        "48001" => "api功能未授权，请确认小程序已获得该接口"
    );
    
    public static function codeToMsg($code)
    {
        return self::$status[$code];
    }
}