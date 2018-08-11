<?php
class Func{
    
    /*
     * 判断请求是否是GET
     */
    public static function isGET()
    {
        return $_SERVER['REQUEST_METHOD'] == 'GET' ? true : false;
    }
    
    /**
     * 记录日志
     * @param string $content
     *
     *
     */
    public static function log($content,$objName)
    {
        $funcTree = debug_backtrace();

        $funcTree = $funcTree[1];
        
        $endDir = "";
        if(isset($funcTree["class"]))$endDir .= $funcTree["class"]."/";
        if(isset($funcTree["function"]))$endDir .= $funcTree["function"]."/";
        
        $dirTree = explode("\\", __FILE__);
        unset($dirTree[count($dirTree) -1]);
        
        $logDir = implode("/", $dirTree)."/".$objName."/log/";
        
        $logDir .= $endDir;
        
        $logContent = "\r\n".date("Y-m-d H:i:s")."\r\n";
        $logContent .= self::aryToStr($content);
        $logContent .= "\r\n";
        if(!file_exists($logDir))mkdir($logDir,null,true);
        file_put_contents($logDir.date("Ymd").".txt", $logContent, FILE_APPEND);
    }
    
    /**
     * 将数组转化为json对象字符串 并非json编码
     * 方式二
     * @param unknown $ary
     */
    public static function aryToJsonStr2($ary)
    {
        $jsonKeyValue = self::getAryJsonKeyValue($ary);
        $json = json_encode($ary);
        
        foreach ($jsonKeyValue as $key => $value)
        {
            $json = str_replace($key, '"'.$value.'"', $json);
        }
        
        return $json;
    }

    public static function getAryJsonKeyValue($ary)
    {
        $resultAry = array();
        foreach ($ary as $index => $value)
        {
            if(is_array($value))
            {
                $resultAry = array_merge($resultAry, self::getAryJsonKeyValue($value));
            }
            else
            {
                $resultAry[json_encode($value)] = $value;
            }
        }
        return $resultAry;
    }
    
    /**
     * 将数组转化为json对象字符串 并非json编码
     * @param unknown $ary
     */
    public static function aryToJsonStr($ary)
    {
        $jsonAry = array();
        return "{".self::aryToStr($ary)."}";
    }
    
    public static function aryToStr($ary)
    {
        $kevalueList = array();
        if(is_array($ary))
        {
            foreach ($ary as $key1 => $value1)
            {
                if(is_array($value1))
                {
                    $value1 = '{'.self::aryToStr($value1).'}';
                    $kevalueList []= '"'.$key1.'":'.$value1;
                }
                else
                {
        
                    $kevalueList []= '"'.$key1.'":"'.$value1.'"';
                }
            }
            return  implode(",", $kevalueList);
        }
        else
        {
            return $ary;
        }
    }
    
    /**
     * XML转数组
     */
    public static function xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $val = json_decode(json_encode($xmlstring),true);
        return $val;
    }
    
    /**
     * 删除文件夹以及文件夹中的文件
     * @param unknown $dir
     * * @param unknown $deep  是否要删除子文件夹以里面的文件
     */
    public static function deleteDirTree($dir,$deep = false)
    {
        //如果是文件夹
        if(is_dir($dir))
        {
            //扫描该文件夹下面的内容
            $files = scandir($dir);
            foreach ($files as $i => $file)
            {
                if($file != "." && $file != "..")
                {
                    //如果是个子文件夹 并且需要删除
                    if(is_dir($dir.$file))
                    {
                        if($deep)
                        {
                            self::deleteDirTree($dir.$file."/",$deep);
                            rmdir($dir.$file);
                        }
                    }
                    else
                    {
                        unlink($dir.$file);
                    }
                }
            }
            if($deep)rmdir($dir);
        }
    }
    
    public static function request($url, $data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}