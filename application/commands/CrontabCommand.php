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
            $argc = $input->getArgument('procedure');
            $result = Db::query(sprintf('call %s', $argc));
        } catch (\Exception $e) {
            $output->error($e->getMessage());
        }
    }

}