<?php
namespace App;


class Message
{

    const SPBC = 'spbc';
    const CHATMSG = 'chatmsg';
    const UENTER = 'uenter';
    const SRRES = 'srres';
    const UPGRADE = 'upgrade';
    const SSD = 'ssd';

    public static function handle($msg) {
        preg_match('/type@=(.*?)\//', $msg, $match);
        $type = $match[1];
        if (empty($type)) return;

        switch($type) {
            //礼物广播
            case self::SPBC:
                preg_match('/\/sn@=(.*?)\/dn@=(.*?)\/gn@=(.*?)\/gc@=(.*?)\//', $msg, $result);
                $from = $result[1] ?? '';
                $to = $result[2] ?? '';
                $gift = $result[3] ?? '';
                $giftNum = $result[4] ?? '';
                echo date("Y-m-d H:i:s"). ' [' . $from. '] 送给了 ['. $to. '] ' . $giftNum.  '个'. $gift . "\n";
                break;
            //弹幕消息
            case self::CHATMSG:
                preg_match_all('/\/nn@=(.*?)\/txt@=(.*?)\//', $msg, $result, PREG_SET_ORDER);
                foreach ($result as $item) {
                    $name = $item[1] ?? '';
                    $text = $item[2] ?? '';
                    echo date("Y-m-d H:i:s"). ' ['. $name .']: '. $text . "\n";
                }
                break;
            //用户进入房间
            case self::UENTER:
                preg_match('/\/nn@=(.*?)\//', $msg, $result);
                $name = $result[1] ?? '';
                echo date("Y-m-d H:i:s"). ' ['. $name .'] 进入房间' . "\n";
                break;
            //分享房间
            case self::SRRES:
                preg_match('/\/nickname@=(.*?)\//', $msg, $result);
                $name = $result[1] ?? '';
                echo date("Y-m-d H:i:s"). ' ['. $name .'] 分享了直播间' . "\n";
                break;
            //用户等级提升
            case self::UPGRADE:
                preg_match('/\/nn@=(.*?)\/level@=(.*?)\//', $msg, $result);
                $name = $result[1] ?? '';
                $level = $result[2] ?? '';
                echo date("Y-m-d H:i:s"). ' 恭喜 ['. $name .'] 升级到'. $level . "\n";
                break;
            //超级弹幕
            case self::SSD:
                preg_match('/\/content@=(.*?)\//', $msg, $result);
                $content = $result[1] ?? '';
                echo date("Y-m-d H:i:s"). ' 超级弹幕 '. $content . "\n";
                break;
            //贵族广播消息
            case 'online_noble_list':
            //心跳
            case 'mrkl':
            //登录返回
            case 'loginres':
            //赠送礼物，没有返回名称，不显示
            case 'dgb':

            //未知
            case 'pingreq':
            case 'noble_num_info':
            case 'rnewbc':
            case 'bgbc':
            case 'synexp':
            case 'qausrespond':
            case 'gbroadcast':
            case 'anbc':
            case 'rwdbc':
                break;

            default:
                if (DEBUG == true)
                    var_dump($type);
                echo '原始数据: '.$msg. "\n";
        }
    }

}