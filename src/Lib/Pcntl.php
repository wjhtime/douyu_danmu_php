<?php
namespace App\Lib;

/**
 * 多进程实现
 * Class Pcntl
 * @package App
 */
class Pcntl
{


    public static function handle()
    {
        $ip = Douyu::ip();
        $port = Douyu::PORT;

        $sock = Sock::instance($ip, $port);
        $pid = pcntl_fork();
        pcntl_signal(SIGINT, function ($signal) use ($sock, $pid) {
            if ($signal == SIGINT) {
                $sock->sendMsg(Douyu::SEND_MSG_LOGOUT);
                posix_kill($pid, SIGKILL);
                echo "good bye \n";
                exit();
            }

        });

        //主进程
        if ($pid) {
            $roomId = 288016;
            $sock->sendMsg(Douyu::packMsg(Douyu::SEND_MSG_LOGIN, $roomId));
            $sock->sendMsg(Douyu::packMsg(Douyu::SEND_MSG_JOIN_ROOM, $roomId));

            while ($content = $sock->read()) {
                //解析，输出内容
                $receiveResult = Message::handle($content);
                array_walk($receiveResult, function ($msg) {
                    if (SHOW_TIME) {
                        $date = date("Y-m-d H:i:s");
                        $msg = $date. ' ' . $msg;
                    }
                    echo $msg;
                });
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
                    $sock->sendMsg(Douyu::SEND_MSG_KEEP_LIVE);
                    $time = time();
                    if (DEBUG === true) {
                        echo date("Y-m-d H:i:s"). " 发送心跳包 \n";
                    }

                }
            }
        }
    }

}


