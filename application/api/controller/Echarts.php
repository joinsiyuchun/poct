<?php

namespace app\api\controller;


use app\common\controller\Api;
use think\Db;
use think\facade\Request;


class Echarts extends API
{
    protected $checkLoginExclude = [
        'return_rate',
        'inspection',
        'cost',
        'revenue',
        'failure_rate',
        'efficiency',
        'benefit',
        'benefit_efficiency_compare',

        'dept',
        'fixcost',
        'varcost',
        'source',
    ];

    public function failure_rate()
    {
        $eid = Request::param('eid', 47);
        $sql = <<<SQL
 SELECT
    item_id as eid, FROM_UNIXTIME(create_time, '%Y-%m') as ctime, COUNT(*) as times
FROM
    think_workorder
WHERE item_id = ?
GROUP BY item_id, FROM_UNIXTIME(create_time, '%Y-%m')

SQL;
        $result = Db::query($sql, [$eid]);
        $response = [];

        $tmpArr = [];
        foreach ($result as $row) {
            $eid = $row['eid'];
            $ctime = $row['ctime'];
            $times = $row['times'];
            $tmpArr[$eid][] = [
                'key' => $ctime,
                'value' => $times
            ];
        }

        foreach ($tmpArr as $name => $data) {
            $xAxis = [];
            $series = [];
            foreach ($data as $td) {
                $xAxis[] = $td['key'];
                $series[] = $td['value'];
            }
            $response[$name]['xAxis'] = $xAxis;
            $response[$name]['series'] = $series;
        }

        return $response;
    }

    public function efficiency()
    {
        return [
            'xAxis' => ['2020-01', '2020-02', '2020-03', '2020-04', '2020-05', '2020-06', '2020-07', '2020-08'],
            'series' => [100, 200, 300, 400, 500, 600, 700, 455]
        ];
    }

    public function benefit()
    {
        return [
            'xAxis' => ['2020-01', '2020-02', '2020-03', '2020-04', '2020-05', '2020-06', '2020-07', '2020-08'],
            'series' => [
                'income' => [333, 1231, 24214, 23424, 2222, 4342, 1344, 455],
                'cost' => [100, 200, 300, 400, 500, 600, 700, 455]
            ]
        ];
    }

    public function revenue()
    {
        return [
            'year' => number_format(999999999),
            'month' => number_format(111111111)
        ];
    }

    public function cost()
    {
        return [
            'year' => number_format(88888888888),
            'month' => number_format(22222222)
        ];
    }

    public function inspection()
    {
        return [
            'year' => number_format(55555555),
            'month' => number_format(3333333)
        ];
    }

    public function return_rate()
    {
        return [
            'year' => 666.66,
            'lastYear' => 30.3
        ];
    }

    public function benefit_efficiency_compare()
    {
        return [
            'benefitYearCompare' => 13,
            'benefitRevenueCompare' => 30,
            'benefitCostCompare' => 50,
            'benefitRevenueRate' => 15,
            'efficiencyMaxCompare' => -13,
            'efficiencyMinCompare' => 30,
            'efficiencyAvgCompare' => 50,
        ];
    }

    public function efficiency_compare()
    {
        return [
            'maxComparePercent' => -13,
            'minComparePercent' => 30,
            'avgComparePercent' => 50,
        ];
    }

    public function dept()
    {
        return
            [
                'code' => 0,

                'data' => [
                    [
                        'id' => 14,
                        'equip_id' => '31',
                        'dept_name' => '科室2',
                        'count' => 501,
                        'profit' => 123456,
                        'cost' => 23451,
                    ]
                ]
            ];
    }

    public function source()
    {
        return
            [
                'code' => 0,

                'data' => [
                    [
                        'id' => 13,
                        'equip_id' => '30',
                        'dept_name' => '科室1',
                        'count' => 50,
                        'profit' => 12345,
                        'cost' => 2345,
                    ]
                ]
            ];
    }

    public function fixcost()
    {
        return
            [
                'code' => 0,

                'data' => [
                    [
                        'id' => 13,
                        'equip_id' => '30',
                        'type' => '人员工资',
                        'start_dt' => '2020-09-01',
                        'end_dt' => '2020-10-01',
                        'amount' => 123123,
                    ]
                ]
            ];
    }

    public function varcost()
    {
        return
            [
                'code' => 0,

                'data' => [
                    [
                        'id' => 13,
                        'equip_id' => '30',
                        'type' => '电费',
                        'start_dt' => '2020-09-01',
                        'end_dt' => '2020-10-01',
                        'amount' => 123,
                    ]
                ]
            ];
    }
}
