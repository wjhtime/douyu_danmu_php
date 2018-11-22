<?php
include 'init.php';
include 'Sock.php';
include 'Message.php';

define('DEBUG', true);




$sock = Sock::instance();
$pid = pcntl_fork();
pcntl_signal(SIGINT, function ($signal) use ($sock, $pid) {
    if ($signal == SIGINT) {
        $sock->sendMsg($sock->msg[Sock::LOGOUT]);
        posix_kill($pid, SIGKILL);
        echo "good bye \n";
        exit();
    }

});



//主进程
if ($pid) {
    $roomId = 288016;
    $sock->sendMsg(sprintf($sock->msg[Sock::LOGIN], $roomId));
    $sock->sendMsg(sprintf($sock->msg[Sock::JOIN_ROOM], $roomId));

    while ($content = $sock->read()) {
        //解析，输出内容
        Message::handle($content);

        pcntl_signal_dispatch();
    }
}
else
//子进程
{
    $time = time();
    //发送心跳包
    while (true) {
        if (time() - $time > 40) {
            $sock->sendMsg($sock->msg[Sock::KEEP_LIVE]);
            $time = time();
            if (DEBUG === true) {
                echo date("Y-m-d H:i:s"). " 发送心跳包 \n";
            }

        }
    }
}






