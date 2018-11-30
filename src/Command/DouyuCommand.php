<?php
namespace App\Command;

use App\Lib\Douyu;
use App\Lib\Message;
use Swoole\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DouyuCommand extends Command
{

    protected function configure()
    {
        $this->setName('danmu');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
//        $io->title('弹幕列表');

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


        $client->on('receive', function ($cli, $data) use ($io, $output) {
            $receiveResult = Message::handle($data);
            array_walk($receiveResult['msg'], function ($msg) use ($io, $output) {
                if (SHOW_TIME) {
                    $date = date("Y-m-d H:i:s");
                    $msg = $date. ' ' . $msg;
                }
                $output->writeln($msg);
            });

        });

        $client->on("error", function($cli){
            echo "Connect failed\n";
        });

        $client->on("close", function($cli){
            echo "Connection close\n";
        });

        $client->connect($ip, $port, 1);

        swoole_timer_tick(45000, function () use ($client) {
            /**
             * @var Client $client
             */
            $client->send(Douyu::packMsg(Douyu::KEEP_LIVE));
//            echo "发送心跳\n";
        });
    }



}