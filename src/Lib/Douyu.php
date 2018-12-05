<?php

namespace App\Lib;

class Douyu
{

    // 斗鱼配置信息
    const SITE_NAME       = "openbarrage.douyutv.com";
    const PORT            = 8601;
    const DEFAULT_ROOM_ID = 288016;

    // 消息
    const SEND_MSG_LOGIN     = "type@=loginreq/roomid@=:msg/\0";
    const SEND_MSG_KEEP_LIVE = "type@=mrkl/\0";
    const SEND_MSG_JOIN_ROOM = "type@=joingroup/rid@=:msg/gid@=-9999/\0";
    const SEND_MSG_LOGOUT    = "type@=logout/\0";

    // 展示信息
    const MSG_ENTER                = '加入房间中...';
    const MSG_LOADING              = '接收弹幕列表...';
    const MSG_ERROR_ROOM_NOT_EXIST = '房间不存在';
    const MSG_ERROR_ROOM_NOT_OPEN  = '主播未开播';

    // 格式标签
    const TAG_INFO = "<fg=green>:msg</>";

    // 房间信息接口
    const ROOM_INFO_URL = "http://open.douyucdn.cn/api/RoomApi/room/%s";
    const ROOM_SEARCH_URL = "http://douyu.tv/search?kw=%s";

    public static function ip()
    {
        return gethostbyname(self::SITE_NAME);

    }

    public static function port()
    {
        return self::PORT;
    }

    /**
     * 打包数据
     *
     * @param $str
     *
     * @return string
     */
    public static function packMsg($str, $params = '')
    {
        $msg    = str_replace(':msg', $params, $str);
        $length = pack('V', 4 + 4 + strlen($msg) + 1);
        $code   = $length;
        $magic  = chr(0xb1) . chr(0x02) . chr(0x00) . chr(0x00);
        $end    = chr(0x00);
        return $length . $code . $magic . $msg . $end;
    }

    /**
     * 展示信息
     *
     * @param $str
     *
     * @return mixed
     */
    public static function showMsg($str)
    {
        return str_replace(':msg', $str, Douyu::TAG_INFO);
    }

}