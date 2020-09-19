<?php

namespace app\api\controller;


use app\common\controller\Api;


class Echarts extends API
{
    protected $checkLoginExclude = ['income', 'item','cost'];

    public function income()
    {
        return [
            'xAxis' => ['2020-01', '2020-02', '2020-03', '2020-04', '2020-05', '2020-06', '2020-07', '2020-08'],
            'series' => [563, 820, 932, 901, 934, 1290, 1330, 1320]
        ];
    }

    public function item()
    {
        return [
            'xAxis' => ['2020-01', '2020-02', '2020-03', '2020-04', '2020-05', '2020-06', '2020-07', '2020-08'],
            'series' => [100, 200, 300, 400, 500, 600, 700, 455]
        ];
    }

    public function cost()
    {
        return [
            'xAxis' => ['2020-01', '2020-02', '2020-03', '2020-04', '2020-05', '2020-06', '2020-07', '2020-08'],
            'series' => [100, 200, 300, 400, 500, 600, 700, 455]
        ];
    }
}
