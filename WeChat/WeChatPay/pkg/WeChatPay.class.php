<?php
require_once 'WeChat/Func.class.php';
class WeChatPay{
    
    public static $default = array(
        "device_info" => "WEB",
        "sign_type" => "MD5",
        "fee_type" => "CNY"
    );
    /**
     * 
     * @param unknown $order 订单数组
     * 
     * @param string $config 配置数组
     * appid:
     * mch_id:
     * 
     * 返回：
     * status:是否成功
     *      [return_code] => SUCCESS
            [return_msg] => OK
            [appid] => 
            [mch_id] => 
            [device_info] => WEB
            [nonce_str] => HId2m8InAj2EUbIG
            [sign] => 
            [result_code] => SUCCESS
            [prepay_id] => 
            [trade_type] => 
            [code_url] => 当trade_type是NATIVE返回该字段
     * msg:失败原因
     * 
     * 统一下单接口
     */
    public static function unifiedOrder($order, $config)
    {
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        $standerOrder = self::tidyData($order, $config);
        if(!$standerOrder["status"])
        {
            return $standerOrder;
        }
        
        $standerOrder = $standerOrder["data"];
        $standerOrder["sign"] = self::signData($standerOrder, $config);
        
        $xml = Func::aryToXML($standerOrder);
        Func::log($xml, "WeChatPay");
        $ans = Func::request($url, $xml);
        Func::log($ans, "WeChatPay");
        
        $ans = Func::xmlToArray($ans);
        $status = true;
        $msg = array();
        if($ans["return_code"] == "FAIL")
        {
            $status = false;
            $msg []= $ans["return_msg"];
        }
        
        return array(
            "status" => $status,
            "msg" => implode(",", $msg),
            "data" => $ans
        );
    }
    
    /**
     * $outTradeNo = '', $transactionId = ''
     * 查询订单
     */
    public static function orderquery($config, $outTradeNo = false, $transactionId = false)
    {
        $url = "https://api.mch.weixin.qq.com/pay/orderquery";
        //验证config配置信息中信息是否满足
        $checkConfig = self::checkWeChatPayConfig($config);
        if(!$checkConfig["status"])
        {
            return $checkConfig;
        }
        
        if($transactionId == '' && $outTradeNo == '')
        {
            return array("status" => false, "msg" => "微信订单号与商户订单号至少传一个");
        }
        
        $postData = array();
        $postData["appid"] = $config["appid"];
        $postData["mch_id"] = $config["mch_id"];
        if($transactionId)$postData["transaction_id"] = $transactionId;
        else $postData["out_trade_no"] = $outTradeNo;
        
        $postData["nonce_str"] = Func::randomStr(32);
        $postData["sign"] = self::signData($postData, $config);
        $xml = Func::aryToXML($postData);
        
        Func::log($xml, "WeChatPay");
        $tmpInfo = Func::request($url, $xml);
   
        Func::log($tmpInfo, "WeChatPay");
        
        $ans = Func::xmlToArray($tmpInfo);
        
        $status = true;
        $msg = array();
        if($ans["return_code"] == "FAIL")
        {
            $status = false;
            $msg []= $ans["return_msg"];
        }
        else if($ans["result_code"] == "FAIL")
        {
            $status = false;
            $msg []= $ans["err_code"]."【".self::tradeStateToMsg($ans["err_code"])."】";
        }
        else 
        {
            $msg[]= self::tradeStateToMsg($ans["trade_state"]);
        }
        
        
        return array(
            "status" => $status,
            "msg" => implode(",", $msg),
            "data" => $ans
        );
    }
    
    /**
     * 退款
     * @param unknown $config
     * @param unknown $refundOrder
     * 注意这里的config中的url为退款通知接收url
     */
    public static function refund($config, $refundOrder)
    {
        $url = "https://api.mch.weixin.qq.com/secapi/pay/refund";
        
        //整理数据为正确订单信息
        $standerOrder = self::tidyrefundData($refundOrder, $config);
        if(!$standerOrder["status"])
        {
            return $standerOrder;
        }
        $standerOrder = $standerOrder["data"];
        $standerOrder["sign"] = self::signData($standerOrder, $config);
        
        $xml = Func::aryToXML($standerOrder);
        
        Func::log($xml, "WeChatPay");
        $ans = Func::request($url, $xml);
        Func::log($ans, "WeChatPay");
        
        $ans = Func::xmlToArray($ans);
        
        $status = true;
        $msg = array();
        if($ans["return_code"] == "FAIL")
        {
            $status = false;
            $msg []= $ans["return_msg"];
        }
        
        return array(
            "status" => $status,
            "msg" => implode(",", $msg),
            "data" => $ans
        );
        return $standerOrder;
    }
    
    /**
     * 关闭订单
     * @param unknown $outTradeNo
     */
    public static function closeorder($config, $outTradeNo)
    {
        $url = "https://api.mch.weixin.qq.com/pay/closeorder";
        //验证config配置信息中信息是否满足
        $checkConfig = self::checkWeChatPayConfig($config);
        if(!$checkConfig["status"])
        {
            return $checkConfig;
        }
        
        $postData = array();
        $postData["appid"] = $config["appid"];
        $postData["mch_id"] = $config["mch_id"];
        $postData["nonce_str"] = Func::randomStr(32);
        $postData["out_trade_no"] = $outTradeNo;
        $postData["sign"] = self::signData($postData, $config);
        
        $xml = Func::aryToXML($postData);
        
        Func::log($xml, "WeChatPay");
        $tmpInfo = Func::request($url, $xml);
        Func::log($tmpInfo, "WeChatPay");
        
        $ans = Func::xmlToArray($tmpInfo);
        
        $status = true;
        $msg = array();
        if($ans["return_code"] == "FAIL")
        {
            $status = false;
            $msg []= $ans["return_msg"];
        }
        else if($ans["result_code"] == "FAIL")
        {
            $status = false;
            $msg []= $ans["err_code"]."【".self::tradeStateToMsg($ans["err_code"])."】";
        }
        
        return array(
            "status" => $status,
            "msg" => implode(",", $msg),
            "data" => $ans
        );
    }
    
    /**
     * 签名数据
     * @param unknown $standardData
     * @param unknown $config
     * @return string
     */
    public static function signData($standardData, $config)
    {
        
        foreach ($standardData as $key => $value)
        {
            if(trim($value) == '' || $key == 'sign')unset($standardData[$key]);
        }
        
        ksort($standardData);
        $stringA = self::toUrlParams($standardData);
        $stringSignTemp = $stringA."&key=".$config["key"];
        $signValue = strtoupper(md5($stringSignTemp));
        return $signValue;
    }
    /**
     * 将数据拼接为url用于签名
     * @param unknown $data
     * @return string
     */
    public static function toUrlParams($data)
    {
        $buff = "";
        foreach ($data as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }
    
        $buff = trim($buff, "&");
        return $buff;
    }
    
    public static function tidyrefundData($refundOrder,$config)
    {
        //验证config配置信息中信息是否满足
        $checkConfig = self::checkWeChatPayConfig($config);
        if(!$checkConfig["status"])
        {
            return $checkConfig;
        }
        
        //验证退款订单信息是否正确
        $refundOrder["sign_type"] = isset($refundOrder["sign_type"]) ? $refundOrder["sign_type"] : self::$default["sign_type"];
        $refundOrder["fee_type"] = isset($refundOrder["fee_type"]) ? $refundOrder["fee_type"] : self::$default["fee_type"];
        $refundOrder["nonce_str"] = Func::randomStr(32);
        
        $checkRefundOrder = self::checkRefundOrder($refundOrder);
        if(!$checkRefundOrder["status"])
        {
            return $checkRefundOrder;
        }
        
        $standerOrderData = array_merge($config,$refundOrder);
        
        $validKey = ["appid","mch_id","nonce_str","sign","sign_type","transaction_id","out_trade_no","out_refund_no","total_fee","refund_fee","refund_fee_type","refund_desc","refund_account","notify_url"];
        foreach ($standerOrderData as $key => $value)
        {
            if(!in_array($key, $validKey))unset($standerOrderData[$key]);
        }
        return array(
            "status" => true,
            "data" => $standerOrderData
        );
    }
    /**
     * 整理数据
     * @param unknown $order
     * @param unknown $config
     * @return multitype:string boolean |multitype:boolean unknown
     */
    public static function tidyData($order,$config)
    {
        //系统辅助填写
        $order = self::assistInput($order);
        
        //验证config配置信息中信息是否满足
        $checkConfig = self::checkWeChatPayConfig($config);
        if(!$checkConfig["status"])
        {
            return $checkConfig;
        }
        
        //检查订单信息是否满足条件
        $checkOrder = self::checkUnifiedOrder($order, $config);
        if(!$checkOrder["status"])
        {
            return $checkOrder;
        }
        
        $standerOrderData = array_merge($config,$order);
        
        //移除掉不应该有的键
        $validKey = ["appid","mch_id","device_info","nonce_str","sign","sign_type","body","detail","attach","out_trade_no","fee_type","total_fee","spbill_create_ip","time_start","time_expire","goods_tag","notify_url","trade_type","product_id","limit_pay","openid"];
        foreach ($standerOrderData as $key => $value)
        {
            if(!in_array($key, $validKey))unset($standerOrderData[$key]);
        }
        
        return array(
            "status" => true,
            "data" => $standerOrderData
        );
    }
    
    /**
     * 填写系统默认信息
     * @param unknown $order
     * @return unknown
     */
    public static function assistInput($order)
    {
        $order["device_info"] = isset($order["device_info"]) ? $order["device_info"] : self::$default["device_info"];
        $order["sign_type"] = isset($order["sign_type"]) ? $order["sign_type"] : self::$default["sign_type"];
        $order["fee_type"] = isset($order["fee_type"]) ? $order["fee_type"] : self::$default["fee_type"];
        
        $order["nonce_str"] = Func::randomStr(32);
        $order["spbill_create_ip"] = self::getSpbillCreateIp();
        
        
        return $order;
    }
    
    /**
     * 检查传递进来的原始订单数据是否满足条件
     * @param unknown $order
     */
    public static function checkUnifiedOrder($order, $config)
    {
        $needData = array();
        $needData["out_trade_no"] = "商户订单号";
        $needData["nonce_str"] = "随机字符串";
        $needData["body"] = "商品描述";
        $needData["total_fee"] = "标价金额";
        $needData["spbill_create_ip"] = "终端IP";
        $needData["attach"] = "附加数据";//虽然此项为必填，但是我还是作为必填项
        
        if($config["trade_type"] == "NATIVE")
        {
            $needData["product_id"] = "trade_type=NATIVE时商品ID【product_id】必填";
        }
        
        if($config["trade_type"] == "JSAPI")
        {
            $needData["openid"] = "trade_type=JSAPI时用户标识【openid】必填";
        }
        
        $status = true;
        $msg = array();
        foreach ($needData as $key => $value)
        {
            if(!isset($order[$key]) || trim($order[$key]) == '')
            {
                $status = false;
                $msg []= $value."【{$key}】缺失";
            }
        }
        
        //选填项
        $optionalData = array();
//         $optionalData["fee_type"] = "标价币种";//如果传值了则验证
        if(isset($order["fee_type"]))
        {
            $ans = self::feeType($order["fee_type"]);
            if(!$ans)
            {
                $status = false;
                $msg []= "标价币种【fee_type】错误,只支持".implode(",", array_keys(self::feeType()));
            }
        }
        
//         $optionalData["time_start"] = "交易起始时间";//如果传值了则验证
        if(isset($order["time_start"]))
        {
            if(!is_numeric($order["time_start"]) || strlen($order["time_start"]) != 14)
            {
                $status = false;
                $msg []= "交易起始时间【time_start】格式错误";
            }
        }
        
//         $optionalData["time_expire"] = "交易结束时间";//如果传值了则验证
        if(isset($order["time_expire"]))
        {
            if(!is_numeric($order["time_expire"]) || strlen($order["time_expire"]) != 14)
            {
                $status = false;
                $msg []= "交易结束时间【time_expire】格式错误";
            }
        }

//         $optionalData["device_info"] = "设备号";//不验证

//         $optionalData["goods_tag"] = "订单优惠标记";//暂不支持
           if(isset($order["goods_tag"]))
           {
               $status = false;
               $msg []= "本对接开发暂不支持 订单优惠标记【goods_tag】";
           }
        
//         $optionalData["detail"] = "商品详情";//暂不支持
           if(isset($order["detail"]))
           {
               $status = false;
               $msg []= "本对接开发暂不支持 商品详情【detail】";
           }
        
        return array(
            "status" => $status,
            "msg" => implode(",", $msg)
        );
    }
    
    /**
     * 检查配置下信息是否满足
     * @param unknown $config
     */
    public static function checkWeChatPayConfig($config)
    {
        $needData = array();
        $needData["appid"] = "应用ID";
        $needData["mch_id"] = "商户号";
        $needData["key"] = "商户密钥";
        $needData["notify_url"] = "通知地址";
        $needData["trade_type"] = "交易类型";
        
        $status = true;
        $msg = array();
        foreach ($needData as $key => $value)
        {
            if(!isset($config[$key]) || trim($config[$key]) == '')
            {
                $status = false;
                $msg []= $needData[$key]."【{$key}】 缺失";
            }
        }
        
        if(isset($config["trade_type"]))
        {
            $ans = self::tradeType($config["trade_type"]);
            if(!$ans)
            {
                $status = false;
                $msg []= "交易类型【trade_type】错误,只支持".implode(",", array_keys(self::tradeType()));
            }
        }
        
        //如果传递了limit_pay 
        if(isset($config["limit_pay"]))
        {
            if($config["limit_pay"] != "no_credit")
            {
                $status = false;
                $msg []= "指定支付方式【limit_pay】错误,选填项，只支持 no_credit";
            }
        }
        
        return array(
            "status" => $status,
            "msg" => implode(",", $msg)
        );
    }
    
    /**
     * 检查退款订单信息是否正常
     * @param unknown $refundOrder
     */
    public static function checkRefundOrder($refundOrder)
    {
        $status = true;
        $msg = array();
        
        //微信订单号，商户订单号 二选一
        if((!isset($refundOrder["out_trade_no"]) && !isset($refundOrder["transaction_id"])) || (isset($refundOrder["out_trade_no"]) && isset($refundOrder["transaction_id"])))
        {
            $status = false;
            $msg []= "微信订单号【out_trade_no】，商户订单号【transaction_id】 二选一";
        }
        
        if(!isset($refundOrder["out_refund_no"]))
        {
            $status = false;
            $msg []= "商户退款单号【out_refund_no】缺失";
        }
        
        if(!isset($refundOrder["total_fee"]))
        {
            $status = false;
            $msg []= "订单金额【total_fee】缺失";
        }
        
        if(!isset($refundOrder["refund_fee"]))
        {
            $status = false;
            $msg []= "退款金额【refund_fee】缺失";
        }

        
        //如果传递了该选填项
        if(isset($refundOrder["refund_fee_type"]))
        {
            if(!self::feeType($refundOrder["refund_fee_type"]))
            {
                $status = false;
                $msg []= "标价币种【fee_type】错误,只支持".implode(",", array_keys(self::feeType()));
            }
        }
        
        //该项为选填项 但是本系统作为必填操作
        if(!isset($refundOrder["refund_desc"]))
        {
            $status = false;
            $msg []= "退款原因【refund_desc】 缺失";
        }
        
        //退款资金来源 选填项
        if(isset($refundOrder["refund_account"]))
        {
            if(!self::refundAccount($refundOrder["refund_account"]))
            {
                $status = false;
                $msg []= "退款资金来源【refund_account】错误,只支持".implode(",", array_keys(self::refundAccount()));
            }
        }
        
        return array("status" => $status,"msg" => implode(",", $msg));
    }
    
    /**
     * 标价币种
     * @param string $key
     */
    public static function feeType($key = false)
    {
        $feeType = array();
        $feeType["CNY"] = "人民币";
        
        if($key)
        {
            if(!isset($feeType[$key]))return false;
            else return $feeType[$key];
        }
        return $feeType;
    }
    /**
     * 退款资金来源
     * @param string $key
     */
    public static function refundAccount($key = false)
    {
        $refundAccount = array();
        $refundAccount["REFUND_SOURCE_UNSETTLED_FUNDS"] = "未结算资金退款";
        $refundAccount["REFUND_SOURCE_RECHARGE_FUNDS"] = "可用余额退款";
        
        if($key)
        {
            if(!isset($refundAccount[$key]))return false;
            else return $refundAccount[$key];
        }
        return $refundAccount;
    }
    /**
     * 交易类型
     * @param string $key
     */
    public static function tradeType($key = false)
    {
        $tradeType = array();
        $tradeType["JSAPI"] = "公众号支付";
        $tradeType["NATIVE"] = "原生扫码支付";
        $tradeType["APP"] = "app支付";
        
        if($key)
        {
            if(!isset($tradeType[$key]))return false;
            else return $tradeType[$key];
        }
        return $tradeType;
    }
    /**
     * 获得本机IP地址
     */
    public static function getSpbillCreateIp()
    {
        $ip = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : $_SERVER["SERVER_ADDR"];
        $ip = "171.217.104.96";
        return $ip;
    }
    
    public static function tradeStateToMsg($tradeState)
    {
        $msg = '';
        switch ($tradeState)
        {
            case "ORDERNOTEXIST":$msg = "此交易订单号不存在 ";break;
            case "SYSTEMERROR":$msg = "系统错误";break;
            case "SUCCESS":$msg = "支付成功";break;
            case "REFUND":$msg = "转入退款";break;
            case "NOTPAY":$msg = "未支付";break;
            case "CLOSED":$msg = "已关闭";break;
            case "REVOKED":$msg = "已撤销（刷卡支付）";break;
            case "USERPAYING":$msg = "用户支付中";break;
            case "PAYERROR":$msg = "支付失败";break;
            default:$msg = "";
        }
        return $msg;
    }
}