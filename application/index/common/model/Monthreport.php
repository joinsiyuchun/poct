<?php
/**
 * 管理员权限节点表模型
 */

namespace app\index\common\model;


use think\Model;

class Monthreport extends Model
{
    // 定义主键和数据表

    protected $table = 'item_info_data';


    function item(){
        return $this->belongsTo('Item','item_id');
    }
}