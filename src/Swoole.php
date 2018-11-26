<?php
namespace App;

use \Swoole\Client;

class Swoole
{

    public static function handle()
    {

        $ip = Douyu::ip();
        $port = Douyu::port();
        $roomId = 288016;

        $client = new Client(SWOOLE_SOCK_TCP,SWOOLE_SOCK_ASYNC);
        $client->on('connect', function ($cli) use ($roomId) {
            /**
             * @var Client $cli
             */
            $cli->send(Douyu::packMsg(Douyu::LOGIN, $roomId));
            $cli->send(Douyu::packMsg(Douyu::JOIN_ROOM, $roomId));
        });

        $client->on('receive', function ($cli, $data) {
            Message::handle($data);

//            swoole_timer_tick(45000, function () use ($cli) {
//                $cli->send(Douyu::packMsg(Douyu::KEEP_LIVE));
//            });
        });

        $client->on("error", function($cli){
            echo "Connect failed\n";
        });

        $client->on("close", function($cli){
            echo "Connection close\n";
        });

        $client->connect($ip, $port, 1);



    }

}