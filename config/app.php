<?php
return [
    /**
     *  是否是调试模式
     */
    'debug' => true,

    /**
     * 输出是否展示时间
     */
    'show_time' => true,

    /**
     * 输出内容是否展示颜色样式
     */
    'show_color' => true,

    /**
     * 自定义的命令，
     */
    'commands' => [
        '\App\Command\DouyuCommand',
        '\App\Command\DouyuSearchCommand',
    ],
    /**
     * 日志文件
     */
    'log_file' => APP_ROOT. '/log/debug.log',

];