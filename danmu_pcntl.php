<?php

class Sock
{
    public $sock;
    static $instance;

    // 登录
    const LOGIN = 'login';
    // 保持心跳
    const KEEP_LIVE = 'keep_live';
    //加入房间
    const JOIN_ROOM = 'join_room';
    //登出
    const LOGOUT = 'logout';

    public $msg = [
        self::LOGIN => "type@=loginreq/roomid@=%s/\0",
        self::KEEP_LIVE => 'type@=mrkl/\0',
        self::JOIN_ROOM => 'type@=joingroup/rid@=%d/gid@=-9999/\0',
        self::LOGOUT => 'type@=logout/\0',
    ];

    public function __construct($sock)
    {
        $this->sock = $sock;
    }

    public static function instance()
    {
        if (!self::$instance) {
            $ip = gethostbyname("openbarrage.douyutv.com");
            $port = 8601;
            $sock = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
            socket_connect($sock, $ip, $port);
            self::$instance = new self($sock);
        }

        return self::$instance;
    }

    /**
     * 打包数据
     * @param $str
     * @return string
     */
    private function packMsg($str){
        $length = pack('V', 4 + 4 + strlen($str) + 1);
        $code = $length;
        $magic = chr(0xb1).chr(0x02).chr(0x00).chr(0x00);
        $end = chr(0x00);
        return $length.$code.$magic.$str.$end;
    }

    /**
     * 发送消息
     * @param $msg
     */
    public function sendMsg($msg)
    {
        $packMsg = $this->packMsg($msg);
        socket_write($this->sock, $packMsg, strlen($packMsg));
    }

    /**
     * 读取消息
     * @return string
     */
    public function read()
    {
        return socket_read($this->sock, 1024);
    }

    /**
     * 关闭连接
     */
    public function close()
    {
        return socket_close($this->sock);
    }

}



$sock = Sock::instance();

pcntl_signal(SIGTERM, function () use ($sock) {
    $sock->sendMsg($sock->msg[Sock::LOGOUT]);
});


$pid = pcntl_fork();
//主进程
if ($pid) {
    $roomId = 288016;
    $sock->sendMsg(sprintf($sock->msg[Sock::LOGIN], $roomId));
    $sock->sendMsg(sprintf($sock->msg[Sock::JOIN_ROOM], $roomId));

    while ($content = $sock->read()) {
        preg_match('/nn@=(.*?)\//', $content, $name);
        preg_match('/txt@=(.*?)\/cid/', $content, $text);
        if (empty($name)) continue;
        $name = $name[1]??'';
        $text = $text[1]??'';
        echo date("Y-m-d H:i:s"). ' ['. $name .']: '. $text . "\n";
    }
}
else
//子进程
{
    $time = time();
    //发送心跳包
    while (true) {
        if (time() - $time > 20) {
            $sock->sendMsg($sock->msg[Sock::KEEP_LIVE]);
            $time = time();
            echo date("Y-m-d H:i:s"). " 发送心跳包 \n";
        }
    }
}






