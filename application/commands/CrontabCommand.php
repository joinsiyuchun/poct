<?php

namespace app\commands;


use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class CrontabCommand extends Command
{
    protected function configure()
    {
        $this->setName('cron')->setDescription('通过命令行执行存储过程');
        $this->addArgument('procedure');
    }

    protected function execute(Input $input, Output $output)
    {
        try {
            $procedure = $input->getArgument('procedure');
            $result = Db::query(sprintf('call CostInfo()', $procedure));
            $output->info(json_encode($result, JSON_PRETTY_PRINT));
            $result = Db::query(sprintf('call ItemInfo()', $procedure));
            $output->info(json_encode($result, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            $output->error($e->getMessage());
        }
    }

}