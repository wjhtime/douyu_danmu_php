<?php
namespace App\Lib;

class Douyu
{

    static $url = "openbarrage.douyutv.com";
    static $port = 8601;

    // 登录
    const LOGIN = 'login';
    // 保持心跳
    const KEEP_LIVE = 'keep_live';
    //加入房间
    const JOIN_ROOM = 'join_room';
    //登出
    const LOGOUT = 'logout';

    public static $msg = [
        self::LOGIN => "type@=loginreq/roomid@=%s/\0",
        self::KEEP_LIVE => 'type@=mrkl/\0',
        self::JOIN_ROOM => 'type@=joingroup/rid@=%d/gid@=-9999/\0',
        self::LOGOUT => 'type@=logout/\0',
    ];


    public static function ip()
    {
        return gethostbyname(self::$url);

    }

    public static function port()
    {
        return self::$port;
    }

    /**
     * 打包数据
     * @param $str
     * @return string
     */
    public static function packMsg($str, ...$params){
        $msg = vsprintf(self::$msg[$str], $params);
        $length = pack('V', 4 + 4 + strlen($msg) + 1);
        $code = $length;
        $magic = chr(0xb1).chr(0x02).chr(0x00).chr(0x00);
        $end = chr(0x00);
        return $length.$code.$magic.$msg.$end;
    }

}