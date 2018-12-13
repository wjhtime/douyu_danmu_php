<?php

namespace App\Lib;

/**
 * 斗鱼消息处理
 * Class Message
 * @package App
 */
class Message
{

    //斗鱼原类型
    const SPBC        = 'spbc';
    const CHATMSG     = 'chatmsg';
    const UENTER      = 'uenter';
    const SRRES       = 'srres';
    const UPGRADE     = 'upgrade';
    const SSD         = 'ssd';
    const NEWBLACKRES = 'newblackres';

    //自定义类型
    const TYPE_GIFT           = 'gift';
    const TYPE_CHAT_MSG       = 'chat_msg';
    const TYPE_USER_ENTER     = 'user_enter';
    const TYPE_SHARE_ROOM     = 'share_room';
    const TYPE_USER_LEVEL_UP  = 'user_level_up';
    const TYPE_SUPER_CHAT_MSG = 'super_chat_msg';
    const TYPE_BANNED         = 'banned';
    const TYPE_ERROR          = 'error';

    //格式化消息
    const TAG_YELLOW = "<fg=yellow>:message</>";
    //青色
    const TAG_CYAN  = "<fg=cyan>:message</>";
    const TAG_BLUE  = "<fg=blue>:message</>";
    const TAG_GREEN = "<fg=green>:message</>";
    const TAG_RED   = "<fg=red>:message</>";
    //品红
    const TAG_MAGENTA = "<fg=magenta>:message</>";
    const TAG_ERROR   = "<fg=cyan;options=blink>:message</>";

    /**
     * 弹幕消息处理
     *
     * @param $msg
     *
     * @return mixed|void
     */
    public static function handle($msg)
    {
        preg_match('/type@=(.*?)\//', $msg, $match);
        $type = $match[1];
        if (empty($type)) return;
        $result = [];

        switch ($type) {
            //礼物广播
            case self::SPBC:
                preg_match('/\/sn@=(.*?)\/dn@=(.*?)\/gn@=(.*?)\/gc@=(.*?)\//', $msg, $matches);
                $from            = $matches[1] ?? '';
                $to              = $matches[2] ?? '';
                $gift            = $matches[3] ?? '';
                $giftNum         = $matches[4] ?? '';
                $result['type']  = self::TYPE_GIFT;
                $result['msg'][] = '[' . $from . '] 送给了 [' . $to . '] ' . $giftNum . '个' . $gift;
                break;
            //弹幕消息
            case self::CHATMSG:
                preg_match_all('/\/nn@=([^\/]*?)\/txt@=([^\/]*?)\//', $msg, $matches, PREG_SET_ORDER);
                $result['type'] = self::TYPE_CHAT_MSG;
                foreach ($matches as $item) {
                    $name            = $item[1] ?? '';
                    $text            = $item[2] ?? '';
                    $result['msg'][] = '[' . $name . ']: ' . $text;
                }
                break;
            //用户进入房间
            case self::UENTER:
                preg_match('/\/nn@=(.*?)\//', $msg, $matches);
                $name            = $matches[1] ?? '';
                $result['type']  = self::TYPE_USER_ENTER;
                $result['msg'][] = '[' . $name . '] 进入房间';
                break;
            //分享房间
            case self::SRRES:
                preg_match('/\/nickname@=(.*?)\//', $msg, $matches);
                $name            = $matches[1] ?? '';
                $result['type']  = self::TYPE_SHARE_ROOM;
                $result['msg'][] = '[' . $name . '] 分享了直播间';
                break;
            //用户等级提升
            case self::UPGRADE:
                preg_match('/\/nn@=(.*?)\/level@=(.*?)\//', $msg, $matches);
                $name            = $matches[1] ?? '';
                $level           = $matches[2] ?? '';
                $result['type']  = self::TYPE_USER_LEVEL_UP;
                $result['msg'][] = '恭喜 [' . $name . '] 升级到' . $level;
                break;
            //超级弹幕
            case self::SSD:
                preg_match('/\/content@=(.*?)\//', $msg, $matches);
                $content         = $matches[1] ?? '';
                $result['type']  = self::TYPE_SUPER_CHAT_MSG;
                $result['msg'][] = '超级弹幕 ' . $content;
                break;
            //禁言
            case self::NEWBLACKRES:
                preg_match('/\/snic@=(.*?)\/dnic@=(.*?)\//', $msg, $matches);
                $user1           = $matches[1] ?? '';
                $user2           = $matches[2] ?? '';
                $result['type']  = self::TYPE_BANNED;
                $result['msg'][] = '[' . $user1 . '] 将 [' . $user2 . '] 禁言';
                break;
            //贵族广播消息
            case 'online_noble_list':
                //心跳
            case 'mrkl':
                //登录返回
            case 'loginres':
                //赠送礼物，没有返回名称，不显示
            case 'dgb':
                //广播排行榜消息
            case 'ranklist':
                //房间内 top10 变化消息
            case 'rankup':
                //栏目排行榜变更通知
            case 'rri':

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
            case 'lgpoolsite':
            case 'blab':
            case 'fswrank':
            case 'cthn':
            case 'wirt':
            case 'rquizisn':
            case 'tsgs':
            case 'rquiziln':
            case 'tkrquizisn':
                break;

            default:
                if (DEBUG == TRUE) {
                    $result['type']  = self::TYPE_ERROR;
                    $result['msg'][] = '原始数据: ' . $msg;
                }
        }

        if (SHOW_COLOR) {
            return self::styleMessage($result);
        }
        return $result;
    }

    /**
     * 格式化弹幕
     *
     * @param $msgResult
     *
     * @return mixed
     */
    public static function styleMessage($msgResult)
    {
        switch ($msgResult['type']) {

            case self::TYPE_GIFT:
            case self::TYPE_USER_LEVEL_UP:
            case self::TYPE_SUPER_CHAT_MSG:
            case self::TYPE_BANNED:
                array_walk($msgResult['msg'], function (&$msg) {
                    $msg = str_replace(':message', $msg, self::TAG_RED);
                });
                break;
            case self::TYPE_CHAT_MSG:
                array_walk($msgResult['msg'], function (&$msg) {
                    $msg = str_replace(':message', $msg, self::TAG_MAGENTA);
                });
                break;
            case self::TYPE_USER_ENTER:
                array_walk($msgResult['msg'], function (&$msg) {
                    $msg = str_replace(':message', $msg, self::TAG_YELLOW);
                });
                break;
            case self::TYPE_SHARE_ROOM:
                array_walk($msgResult['msg'], function (&$msg) {
                    $msg = str_replace(':message', $msg, self::TAG_BLUE);
                });
                break;
            case self::TYPE_ERROR:
                array_walk($msgResult['msg'], function (&$msg) {
                    $msg = str_replace(':message', $msg, self::TAG_ERROR);
                });
                break;
        }

        return $msgResult;
    }


}