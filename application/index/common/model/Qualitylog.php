<?php
/**
 * 管理员登录日志表模型
 */

namespace app\index\common\model;


use think\Model;

class Qualitylog extends Model
{
    // 定义主键和数据表
    protected $pk = 'id';
    protected $table = 'think_quality_log';


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