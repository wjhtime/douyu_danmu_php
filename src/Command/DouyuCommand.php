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
    protected $roomInfo = [];

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
        $ip     = Douyu::ip();
        $port   = Douyu::PORT;
        $roomId = $input->getArgument('room_id');

        $io = new SymfonyStyle($input, $output);

        if (!$this->checkRoomExist($roomId)) {
            return $io->error(Douyu::MSG_ERROR_ROOM_NOT_EXIST);
        }

        if ($this->roomInfo['data']['room_status'] == 2) {
            return $io->error(Douyu::MSG_ERROR_ROOM_NOT_OPEN);
        }
        $output->writeln(Douyu::showMsg(Douyu::MSG_ENTER));

        $client = new Client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
        // 连接
        $client->on('connect', function ($cli) use ($roomId, $output) {
            $cli->send(Douyu::packMsg(Douyu::SEND_MSG_LOGIN, $roomId));
            $cli->send(Douyu::packMsg(Douyu::SEND_MSG_JOIN_ROOM, $roomId));
            $output->writeln(Douyu::showMsg(Douyu::MSG_LOADING));
        });

        // 接收数据
        $client->on('receive', function ($cli, $data) use ($output) {
            $receiveResult        = Message::handle($data);
            $receiveResult['msg'] = $receiveResult['msg'] ?? [];
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
            $cli->send(Douyu::packMsg(Douyu::SEND_MSG_LOGOUT));
            echo "Connection close\n";
        });

        $client->connect($ip, $port, 1);

        //设置定时器，发送心跳
        swoole_timer_tick(45000, function () use ($client) {
            $client->send(Douyu::packMsg(Douyu::SEND_MSG_KEEP_LIVE));
        });
    }

    /**
     * 验证房间号是否存在
     *
     * @param $roomId
     *
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function checkRoomExist($roomId)
    {
        $client = new \GuzzleHttp\Client();
        $res    = $client->request('GET', sprintf(Douyu::ROOM_INFO_URL, $roomId));
        $body   = json_decode($res->getBody(), TRUE);
        if ($body['error'] == 0) {
            $this->roomInfo = $body;
            return TRUE;
        }
        return FALSE;
    }


}