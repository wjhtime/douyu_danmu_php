<?php

namespace App\Command;

use App\Lib\Douyu;
use QL\QueryList;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tightenco\Collect\Support\Collection;

class DouyuSearchCommand extends Command
{
    /**
     * 配置命令相关信息
     */
    protected function configure()
    {
        $this->setName('douyu:search')
             ->setDescription('斗鱼搜索，获取指定房间的信息')
             ->addArgument('keywords', InputArgument::REQUIRED, '名称');
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
        $keywords = $input->getArgument('keywords');

        $res = $this->searchRooms($keywords);

        $table = new Table($output);
        $table->setHeaders(['房间号', '房间名称', '主播名称'])
              ->addRows($res)
              ->render();
    }

    /**
     * 验证房间号是否存在
     * @param $keywords
     *
     * @return Collection
     */
    protected function searchRooms($keywords)
    {
        $ql = QueryList::get(sprintf(Douyu::ROOM_SEARCH_URL, rawurlencode($keywords)));
        $ids = $ql->find('.play-list a')->attrs('data-rid')->toArray();
        $titles = $ql->find('.play-list li a .mes .mes-tit h3')->texts('title')->toArray();
        $names = $ql->find('.play-list li a .mes p .dy-name')->texts('name')->toArray();

        return array_map(null, $ids, $titles, $names);
    }


}
