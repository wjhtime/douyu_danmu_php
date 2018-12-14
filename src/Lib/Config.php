<?php

namespace App\Lib;

class Config implements \ArrayAccess
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
    public static function instance($config = [])
    {
        if (!self::$instance) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }


    public function offsetExists($offset)
    {
        return isset(self::$instance->config[$offset]);
    }

    public function offsetGet($offset)
    {
        return self::$instance->config[$offset];
    }

    public function offsetSet($offset, $value)
    {
        self::$instance->config[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset(self::$instance->config[$offset]);
    }

    public static function get($key)
    {
        return self::$instance->config[$key];
    }

    public function __get($key)
    {
        return self::$instance->config[$key];
    }

}