<?php
namespace App\Lib;


class Sock
{
    public $sock;
    static $instance;

    private $maxLength = 1024;

    public function __construct($sock)
    {
        $this->sock = $sock;
    }

    public static function instance($ip, $port)
    {
        if (!self::$instance) {
            $sock = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
            socket_connect($sock, $ip, $port);
            self::$instance = new self($sock);
        }

        return self::$instance;
    }


    /**
     * 发送消息
     * @param $msg
     */
    public function sendMsg($msg)
    {
        socket_write($this->sock, $msg, strlen($msg));
    }

    /**
     * 读取消息
     * @return string
     */
    public function read()
    {
        return socket_read($this->sock, $this->maxLength);
    }

    /**
     * 关闭连接
     */
    public function close()
    {
        return socket_close($this->sock);
    }

}