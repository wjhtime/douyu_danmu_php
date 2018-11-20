<?php
include 'Sock.php';

ini_set('date.timezone', 'PRC');


$sock = Sock::instance();

pcntl_signal(SIGINT, function ($signal) use ($sock) {
    if ($signal == SIGINT) {
        $sock->sendMsg($sock->msg[Sock::LOGOUT]);
        echo "good bye \n";
        exit();
    }

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

        pcntl_signal_dispatch();
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
        pcntl_signal_dispatch();
    }
}






