<?php


namespace app\index\common\model;


use think\Model;

class Workorder extends Model
{
    // 定义主键和数据表
    protected $pk = 'id';
    protected $table = 'think_workorder';

    // 定义自动时间戳和数据格式
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $dateFormat = 'Y-m-d H:i:s';

    function notification(){
        return $this->belongsTo('Notification','notification_id');
    }

    function items(){
        return $this->belongsTo('Item','item_id');
    }

    function user(){
        return $this->belongsTo('User','receptor_id');
    }
    function finaluser(){
        return $this->belongsTo('User','completed_by');
    }
}