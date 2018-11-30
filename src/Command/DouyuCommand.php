<?php

namespace App\Command;

use App\Lib\Douyu;
use App\Lib\Message;
use Swoole\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DouyuCommand extends Command
{
    const TAG_INFO = "<fg=green>%s</>";

    const MSG_ENTER   = '加入房间中...';
    const MSG_LOADING = '接收弹幕列表...';

    /**
     * 配置命令相关信息
     */
    protected function configure()
    {
        $this->setName('danmu')
             ->setDescription('斗鱼获取弹幕，输入房间id即可获取弹幕，默认显示英雄联盟相关的弹幕')
             ->addArgument('room_id', InputArgument::OPTIONAL, '房间号', 288016);
    }

    /**
     * 执行命令
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf(self::TAG_INFO, self::MSG_ENTER));
        $ip     = Douyu::ip();
        $port   = Douyu::port();
        $roomId = $input->getArgument('room_id');

        $client = new Client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
        // 连接
        $client->on('connect', function ($cli) use ($roomId, $output) {
            /**
             * @var Client $cli
             */
            $cli->send(Douyu::packMsg(Douyu::LOGIN, $roomId));
            $cli->send(Douyu::packMsg(Douyu::JOIN_ROOM, $roomId));
            $output->writeln(sprintf(self::TAG_INFO, self::MSG_LOADING));
        });

        // 接收数据
        $client->on('receive', function ($cli, $data) use ($output) {
            $receiveResult = Message::handle($data);
            array_walk($receiveResult['msg'], function ($msg) use ($output) {
                if (SHOW_TIME) {
                    $date = date("Y-m-d H:i:s");
                    $msg  = $date . ' ' . $msg;
                }
                $output->writeln($msg);
            });

        });

        $client->on("error", function ($cli) {
            echo "Connect failed\n";
        });
        $client->on("close", function ($cli) {
            echo "Connection close\n";
        });

        $client->connect($ip, $port, 1);

        //设置定时器，发送心跳
        swoole_timer_tick(45000, function () use ($client) {
            /**
             * @var Client $client
             */
            $client->send(Douyu::packMsg(Douyu::KEEP_LIVE));
//            echo "发送心跳\n";
        });
    }


}