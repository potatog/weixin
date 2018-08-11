<?php
/***
 * 该类用于向微信发送消息
 * @author Administrator
 *
 */
class WeChatBackMsg{
    public static function backTextMsgToWechatUser($FromUserName, $ToUserName, $Content)
    {
        echo self::toTextMsgXML($FromUserName, $ToUserName, $Content);
    }
    
    /**
     * 回复用户消息 xml格式 拼接 之 文本消息
     * @param unknown $FromUserName 开发者微信号
     * @param unknown $ToUserName 接收方帐号（收到的OpenID）
     * @param unknown $Content 回复的消息内容（换行：在content中能够换行，微信客户端就支持换行显示）
     */
    public static function toTextMsgXML($FromUserName, $ToUserName, $Content)
    {
        $CreateTime = time();
        $XML = "<xml>
        <ToUserName><![CDATA[{$ToUserName}]]></ToUserName>
        <FromUserName><![CDATA[{$FromUserName}]]></FromUserName>
        <CreateTime>{$CreateTime}</CreateTime>
        <MsgType><![CDATA[text]]></MsgType>
        <Content><![CDATA[{$Content}]]></Content>
        </xml>";
        Func::log($XML,"OfficialAccounts");
        return $XML;
    }
     
    public static function backImageMsgToWechatUser($FromUserName, $ToUserName, $media_id)
    {
        echo self::toImageMsgXML($FromUserName, $ToUserName, $media_id);
    }
    /**
     *
     * @param unknown $FromUserName开发者微信号
     * @param unknown $ToUserName接收方帐号（收到的OpenID）
     * @param unknown $media_id 通过素材管理中的接口上传多媒体文件，得到的id。 用户发送的图片消息得到的ID也可以用
     * @return string
     */
    public static function toImageMsgXML($FromUserName, $ToUserName, $media_id)
    {
        $CreateTime = time();
        $XML = "<xml>
        <ToUserName><![CDATA[{$ToUserName}]]></ToUserName>
        <FromUserName><![CDATA[{$FromUserName}]]></FromUserName>
        <CreateTime>{$CreateTime}</CreateTime>
        <MsgType><![CDATA[image]]></MsgType>
        <Image><MediaId><![CDATA[{$media_id}]]></MediaId></Image>
        </xml>";
        Func::log($XML,"OfficialAccounts");
        return $XML;
    }
     
    public static function backVoiceToWeChatUser($FromUserName, $ToUserName, $media_id)
    {
        echo self::toVoiceMsgXML($FromUserName, $ToUserName, $media_id);
    }
    /**
     *
     * @param unknown $FromUserName开发者微信号
     * @param unknown $ToUserName 接收方帐号（收到的OpenID）
     * @param unknown $media_id 通过素材管理中的接口上传多媒体文件，得到的id
     */
    public static function toVoiceMsgXML($FromUserName, $ToUserName, $media_id)
    {
        $CreateTime = time();
        $XML = "<xml>
        <ToUserName><![CDATA[{$ToUserName}]]></ToUserName>
        <FromUserName><![CDATA[{$FromUserName}]]></FromUserName>
        <CreateTime>{$CreateTime}</CreateTime>
        <MsgType><![CDATA[voice]]></MsgType>
        <Voice><MediaId><![CDATA[{$media_id}]]></MediaId></Voice>
        </xml>";
        Func::log($XML,"OfficialAccounts");
        return $XML;
    }
     
     
    public static function backVideoMsgToWeChatUser($FromUserName, $ToUserName, $media_id, $title = '', $description = '')
    {
        echo self::toVideoMsgXML($FromUserName, $ToUserName, $media_id, $title, $description);
    }
    /**
     *
     * @param unknown $FromUserName开发者微信号
     * @param unknown $ToUserName接收方帐号（收到的OpenID）
     * @param unknown $media_id 通过素材管理中的接口上传多媒体文件，得到的id
     * @param string $title 视频消息的标题
     * @param string $description 视频消息的描述
     * @return string
     */
    public static function toVideoMsgXML($FromUserName, $ToUserName, $media_id, $title = '', $description = '')
    {
        $CreateTime = time();
        $XML = "<xml>
        <ToUserName><![CDATA[{$ToUserName}]]></ToUserName>
        <FromUserName><![CDATA[$FromUserName]]></FromUserName>
        <CreateTime>{$CreateTime}</CreateTime>
        <MsgType><![CDATA[video]]></MsgType>
        <Video>
        <MediaId><![CDATA[{$media_id}]]></MediaId>
        <Title><![CDATA[{$title}]]></Title>
        <Description><![CDATA[{$description}]]></Description>
        </Video>
        </xml>";
        Func::log($XML,"OfficialAccounts");
        return $XML;
    }
     
    public static function backMusicMsgToWeChatUser($FromUserName, $ToUserName, $media_id, $MusicUrl='', $HQMusicUrl = '', $title = '',$description = '')
    {
        echo self::toMusicMsgXML($FromUserName, $ToUserName, $media_id, $MusicUrl, $HQMusicUrl, $title,$description);
    }
    /**
     *
     * @param unknown $FromUserName开发者微信号
     * @param unknown $ToUserName接收方帐号（收到的OpenID）
     * @param unknown $media_id缩略图的媒体id，通过素材管理中的接口上传多媒体文件，得到的id
     * @param unknown $MusicUrl音乐链接
     * @param string $HQMusicUrl高质量音乐链接，WIFI环境优先使用该链接播放音乐
     * @param string $title音乐标题
     * @param string $description音乐描述
     */
    public static function toMusicMsgXML($FromUserName, $ToUserName, $media_id, $MusicUrl='', $HQMusicUrl = '', $title = '',$description = '')
    {
        $CreateTime = time();
        $XML = "<xml>
        <ToUserName><![CDATA[{$ToUserName}]]></ToUserName>
        <FromUserName><![CDATA[{$FromUserName}]]></FromUserName>
        <CreateTime>{$CreateTime}</CreateTime>
        <MsgType><![CDATA[music]]></MsgType>
        <Music>
        <Title><![CDATA[{$title}]]></Title>
        <Description><![CDATA[{$description}]]></Description>
        <MusicUrl><![CDATA[{$MusicUrl}]]></MusicUrl>
        <HQMusicUrl><![CDATA[{$HQMusicUrl}]]></HQMusicUrl>
        <ThumbMediaId><![CDATA[{$media_id}]]></ThumbMediaId>
        </Music>
        </xml>";
        Func::log($XML,"OfficialAccounts");
        return $XML;
    }
     
     
    public static function backNewsMsgToWeChatUser($FromUserName, $ToUserName, $Articles)
    {
        echo self::toNewsMsgXML($FromUserName, $ToUserName, $Articles);
    }
    /**
     *
     * @param unknown $fromUser开发者微信号
     * @param unknown $toUser接收方帐号（收到的OpenID）
     * @param unknown $Articles 多条图文消息信息，默认第一个item为大图,注意，如果图文数超过8，则将会无响应
     $Articles = array(
     0 => array(//默认第一个item为大图
     "Title" => ,//图文消息标题
     "Description" =>,//图文消息描述
     "PicUrl" => ,//图片链接，支持JPG、PNG格式，较好的效果为大图360*200，小图200*200
     "Url" => ,//点击图文消息跳转链接
     ),
     1 => array(
     "Title" => ,//图文消息标题
     "Description" =>,//图文消息描述
     "PicUrl" => ,//图片链接，支持JPG、PNG格式，较好的效果为大图360*200，小图200*200
     "Url" => ,//点击图文消息跳转链接
     )
      
     );
     */
    public static function toNewsMsgXML($FromUserName, $ToUserName, $Articles)
    {
        $CreateTime = time();
        $ArticleCount = count($Articles);
         
        $XML = "<xml>
        <ToUserName><![CDATA[{$ToUserName}]]></ToUserName>
        <FromUserName><![CDATA[{$FromUserName}]]></FromUserName>
        <CreateTime>{$CreateTime}</CreateTime>
        <MsgType><![CDATA[news]]></MsgType>
        <ArticleCount>{$ArticleCount}</ArticleCount>
        <Articles>";
        foreach ($Articles as $index => $Article)
        {
            $XML .="<item>
            <Title><![CDATA[{$Article["Title"]}]]></Title>
            <Description><![CDATA[{$Article["Description"]}]]></Description>
            <PicUrl><![CDATA[{$Article["PicUrl"]}]]></PicUrl>
            <Url><![CDATA[{$Article["Url"]}]]></Url>
            </item>";
        }
    
        $XML .="</Articles>
        </xml>";
        Func::log($XML,"OfficialAccounts");
        return $XML;
    }
}