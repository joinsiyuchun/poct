<?php
/**
 * 管理员权限节点表模型
 */

namespace app\index\common\model;


use think\Model;

class Item extends Model
{
    // 定义主键和数据表
    protected $pk = 'id';
    protected $table = 'think_item';

    // 定义自动时间戳和数据格式
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $dateFormat = 'Y-m-d H:i:s';

    function catagory(){
        return $this->belongsTo('Catagory','catagoryid');
    }
}