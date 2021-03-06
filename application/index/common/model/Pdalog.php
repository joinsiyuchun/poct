<?php


namespace app\index\common\model;


use think\Model;

class Pdalog extends Model
{
    // 定义主键和数据表
    protected $pk = 'id';
    protected $table = 'think_pda_log';

    // 定义自动时间戳和数据格式
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $dateFormat = 'Y-m-d H:i:s';


}