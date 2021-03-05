<?php


namespace app\index\common\model;


use think\Model;

class Sample extends Model
{
    // 定义主键和数据表
    protected $pk = 'id';
    protected $table = 'think_poct_sample';

    // 定义自动时间戳和数据格式
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $dateFormat = 'Y-m-d H:i:s';

    function patient(){
        return $this->belongsTo('Patient','patient_id');
    }

    function org(){
        return $this->belongsTo('Org','department_id');
    }

    function item(){
        return $this->belongsTo('Item','item_id');
    }

    function request(){
        return $this->belongsTo('Request','request_id');
    }
}