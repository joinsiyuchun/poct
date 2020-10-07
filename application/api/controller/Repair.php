<?php

namespace app\api\controller;


use app\common\controller\Api;
use think\Db;
use think\facade\Request;


class Repair extends API
{

    protected $checkLoginExclude = ['item_tendency'];

    /**
     * 设备类型，各类型数量、各类型停机数量：
     * @return array
     */
    public function item_tendency()
    {
        $sql = <<<SQL
SELECT 
    DATE_FORMAT(FROM_UNIXTIME(create_time), '%Y-%m') AS t_time,
    COUNT(1) AS amount
FROM
    think_item
GROUP BY t_time;
SQL;
        $result = Db::query($sql);
        $response = [
            'xAxis' => [],
            'series' => []
        ];
        foreach ($result as $row) {
            $ctime = $row['t_time'];
            $times = $row['amount'];
            $response['xAxis'][] = $ctime;
            $response['series'][] = $times;
        }

        return $response;
    }
}
