<?php


namespace app\index\common\model;


use think\Model;

class Hisorder extends Model
{
    // 定义主键和数据表
    protected $pk = 'id';
    protected $table = 'think_hisorder';

    // 定义自动时间戳和数据格式
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $dateFormat = 'Y-m-d H:i:s';

    function org(){
        return $this->belongsTo('Org','department_id');
    }

}