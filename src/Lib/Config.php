<?php

namespace App\Lib;

class Config
{
    protected        $config;
    protected static $instance;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param $config
     *
     * @return Config
     */
    public static function instance($config)
    {
        if (!self::$instance) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    /**
     * 获取配置信息
     *
     * @param $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return self::$instance->config[$key];
    }

}