<?php
namespace App;

use Swoole\Client;

class Swoole
{

    public static function handle()
    {

        $ip = gethostbyname("openbarrage.douyutv.com");
        $port = 8601;
        $roomId = 288016;

        $client = new \Swoole\Client(SWOOLE_SOCK_TCP,SWOOLE_SOCK_ASYNC);
        $client->on('connect', function ($cli) use ($roomId) {
            /**
             * @var Client $cli
             */
            $cli->send(Sock::packMsg(sprintf("type@=loginreq/roomid@=%s/\0", $roomId)));
            $cli->send(Sock::packMsg(sprintf("type@=joingroup/rid@=%d/gid@=-9999/\0", $roomId)));
        });

        $client->on('receive', function ($cli, $data) {
            Message::handle($data);
        });

        $client->on("error", function($cli){
            echo "Connect failed\n";
        });

        $client->on("close", function($cli){
            echo "Connection close\n";
        });

        $client->connect($ip, $port, 1);



//        $sock = Sock::instance();
//        $sock->sendMsg(sprintf($sock->msg[Sock::LOGIN], $roomId));
//        $sock->sendMsg(sprintf($sock->msg[Sock::JOIN_ROOM], $roomId));
//
//        while ($content = $sock->read()) {
//            //解析，输出内容
//            Message::handle($content);
//        }
    }

}