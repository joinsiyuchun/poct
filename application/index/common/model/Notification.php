<?php
/**
 * 机构表模型
 */

namespace app\index\common\model;


use think\Model;



class Notification extends Model
{
    // 定义主键和数据表
    protected $pk = 'id';
    protected $table = 'think_notification';

    // 定义自动时间戳和数据格式
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $dateFormat = 'Y-m-d H:i:s';

    function org(){
        return $this->belongsTo('Org','org_id');
    }

    function user(){
        return $this->belongsTo('User','creater_id');
    }

    function workorder(){
        return $this->hasone('Workorder','notification_id');
    }

}