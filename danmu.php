<?php

class Danmu
{

    public function __construct()
    {

    }

    public function handle()
    {
        $ip = gethostbyname("openbarrage.douyutv.com");
        $port = 8601;

        if(($sock = socket_create(AF_INET,SOCK_STREAM,SOL_TCP)) < 0) {
            echo "socket_create() 失败的原因是:".socket_strerror($sock)."\n";
        }

        if (($result = socket_connect($sock, $ip, $port)) < 0){
            echo "socket_connect() 失败的原因是:".socket_strerror($result) . "\n";
        }

        //登录
        $msg = "type@=loginreq/roomid@=288016/\0";
        $packMsg = $this->packMsg($msg);
        socket_write($sock, $packMsg, strlen($packMsg));

        //获取弹幕请求
        $joinMsg = 'type@=joingroup/rid@=288016/gid@=-9999/\0';
        $packJoinMsg = $this->packMsg($joinMsg);
        socket_write($sock, $packJoinMsg, strlen($packJoinMsg));

        $time = time();

        while($content = socket_read($sock, 1024)){
            preg_match('/nn@=(.*?)\//', $content, $name);
            preg_match('/txt@=(.*?)\/cid/', $content, $text);
            if (empty($name)) continue;
            $name = $name[1]??'';
            $text = $text[1]??'';
            echo date("Y-m-d H:i:s"). ' ['. $name .']: '. $text . "\n";


            if (time()-$time > 20) {
                $this->keepLive($sock);
                $time = time();
            }

        }

        socket_close($sock);
    }


    private function keepLive($sock)
    {
        $keepMsg = 'type@=keeplive/tick@='. time(). '/\0';
        $packKeepMsg = $this->packMsg($keepMsg);
        socket_write($sock, $packKeepMsg, strlen($packKeepMsg));
        echo date("Y-m-d H:i:s"). " 发送心跳包\n";
    }

    private function packMsg($str){
        $length = pack('V', 4 + 4 + strlen($str) + 1);
        $code = $length;
        $magic = chr(0xb1).chr(0x02).chr(0x00).chr(0x00);
        $end = chr(0x00);
        return $length.$code.$magic.$str.$end;
    }


}

$danmu = new Danmu();
$danmu->handle();