<?php


namespace app\index\common\model;


use think\Model;

class Transferlog extends Model
{
    // 定义主键和数据表
    protected $pk = 'id';
    protected $table = 'think_transfer_log';

    // 定义自动时间戳和数据格式
    protected $autoWriteTimestamp = true;
    protected $createTime = 'qc_time';
    protected $dateFormat = 'Y-m-d H:i:s';

    function org(){
        return $this->belongsTo('Org','org_id');
    }

    function user(){
        return $this->belongsTo('User','operator');
    }

    function item(){
        return $this->belongsTo('item','item_id');
    }

}