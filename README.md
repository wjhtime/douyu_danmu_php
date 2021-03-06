# 斗鱼弹幕 PHP版本

之前写过python获取斗鱼的弹幕 [传送门](https://github.com/wjhtime/douyu_danmu_python)，突发奇想，想要用php来实现。弹幕获取实现起来很简单，用swoole很容易做到了，后期也做了一些命令行格式的优化

使用了swoole来连接socket，取代了php的socket的函数，使用起来更加方便灵活，[swoole文档](https://wiki.swoole.com/wiki/page/1.html)

默认获取的英雄联盟的弹幕，如果想要看其他房间的弹幕，只需执行命令 php cli.php danmu <room id>即可

找个人气旺的房间，一起来欣赏爆炸的弹幕吧！

## Requirements
- php7.0
- swoole扩展
- symfony/console
- guzzlehttp/guzzle
- jaeger/querylist

## Quick Start
```
1. pecl install swoole 
省略配置过程，具体参照swoole文档...
2. git clone git@github.com:wjhtime/douyu_danmu_php.git
3. composer install -vvv (安装过程可能较长，通过-vvv查看输出)
4. php cli.php douyu:search <keywords>
5. php cli.php danmu <room_id=288016>
或者 php danmu.php
```

## Feature
- 使用swoole获取弹幕数据
- 使用symfony/console包，内容输出更加美观
- 消息处理，弹幕消息、赠送礼物、分享房间等类型均做处理


## Screenshots
![截图](./images/screen_shot.jpeg)

## Illustrate
![演示](./images/show.gif)

## To Do List
- 弹幕信息分类不够清晰，未知type型数据过多


## CHANGELOG

[CHANGELOG](https://github.com/wjhtime/douyu_danmu_php/releases)


## License

[MIT](https://github.com/wjhtime/douyu_danmu_php/blob/master/LICENSE)