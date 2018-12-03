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
    const TAG_INFO  = "<fg=green>:message</>";
    const TAG_ERROR = "<fg=red>:message</>";

    const MSG_ENTER                = '加入房间中...';
    const MSG_LOADING              = '接收弹幕列表...';
    const MSG_ERROR_ROOM_NOT_EXIST = '房间不存在';
    const MSG_ERROR_ROOM_NOT_OPEN  = '主播未开播';

    const ROOM_INFO_URL = "http://open.douyucdn.cn/api/RoomApi/room/%s";

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
        $port   = Douyu::port();
        $roomId = $input->getArgument('room_id');

        $io = new SymfonyStyle($input, $output);

        if (!$this->checkRoomExist($roomId)) {
            return $io->error(self::MSG_ERROR_ROOM_NOT_EXIST);
        }

        if ($this->roomInfo['data']['room_status'] == 2) {
            return $io->error(self::MSG_ERROR_ROOM_NOT_OPEN);
        }

        $output->writeln(str_replace(':message', self::MSG_ENTER, self::TAG_INFO));
        $client = new Client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
        // 连接
        $client->on('connect', function ($cli) use ($roomId, $output) {
            /**
             * @var Client $cli
             */
            $cli->send(Douyu::packMsg(Douyu::LOGIN, $roomId));
            $cli->send(Douyu::packMsg(Douyu::JOIN_ROOM, $roomId));
            $output->writeln(str_replace(':message', self::MSG_LOADING, self::TAG_INFO));
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
        $res    = $client->request('GET', sprintf(self::ROOM_INFO_URL, $roomId));
        $body   = json_decode($res->getBody(), TRUE);
        if ($body['error'] == 0) {
            $this->roomInfo = $body;
            return TRUE;
        }
        return FALSE;
    }


}