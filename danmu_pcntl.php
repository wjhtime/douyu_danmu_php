<?php

class Danmu
{

    /**
     * 默认房间id
     * @var int
     */
    public $roomId = 288016;

    public $sock;

    public function __construct($roomId)
    {
        $this->roomId = $roomId;

        $ip = gethostbyname("openbarrage.douyutv.com");
        $port = 8601;

        if(($this->sock = socket_create(AF_INET,SOCK_STREAM,SOL_TCP)) < 0) {
            echo "socket_create() 失败的原因是:".socket_strerror($this->sock)."\n";
        }

        if (($result = socket_connect($this->sock, $ip, $port)) < 0){
            echo "socket_connect() 失败的原因是:".socket_strerror($result) . "\n";
        }
    }

    public function handle()
    {
        //登录
        $this->login();

        //获取弹幕请求
        $this->joinRoom();

        while($content = socket_read($this->sock, 1024)){
            preg_match('/nn@=(.*?)\//', $content, $name);
            preg_match('/txt@=(.*?)\/cid/', $content, $text);
            if (empty($name)) continue;
            $name = $name[1]??'';
            $text = $text[1]??'';
            echo date("Y-m-d H:i:s"). ' ['. $name .']: '. $text . "\n";
        }

        socket_close($this->sock);
    }

    private function login()
    {
        $msg = "type@=loginreq/roomid@=".$this->roomId. "/\0";
        $packMsg = $this->packMsg($msg);
        socket_write($this->sock, $packMsg, strlen($packMsg));
    }

    public function keepLive()
    {
        $keepMsg = 'type@=keeplive/tick@='. time(). '/\0';
        $packKeepMsg = $this->packMsg($keepMsg);
        socket_write($this->sock, $packKeepMsg, strlen($packKeepMsg));
        echo date("Y-m-d H:i:s"). " 发送心跳包\n";
    }

    private function joinRoom()
    {
        $joinMsg = 'type@=joingroup/rid@=288016/gid@=-9999/\0';
        $packJoinMsg = $this->packMsg($joinMsg);
        socket_write($this->sock, $packJoinMsg, strlen($packJoinMsg));
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


}


$pid = pcntl_fork();
$roomId = 288016;
//主进程
if ($pid) {
    $danmu = new Danmu($roomId);
    $danmu->handle();
} else {
    $danmu = new Danmu($roomId);
    //子进程
    $time = time();
    //发送心跳包
    while (true) {
        if (time() - $time > 20) {
            $danmu->keepLive();
            $time = time();
        }
    }
}

