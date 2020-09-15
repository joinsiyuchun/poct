<?php
/**
 * 系统设置表模型
 */

namespace app\common\model;


use think\Model;

class Setting extends Model
{
    // 定义主键和数据表
    protected $pk = 'name';
    protected $table = 'think_setting';

    // 定义自动时间戳和数据格式
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $dateFormat = 'Y-m-d H:i:s';


}