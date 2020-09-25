<?php

namespace app\api\controller;


use app\common\controller\Api;
use think\Db;
use think\facade\Request;


class Echarts extends API
{
    const  DEFAULT_EID = 47;
    protected $checkLoginExclude = [
        'return_rate',
        'global_year',
        'global_month',
        'failure_rate',
        'efficiency_last_year',
        'efficiency_current_year',
        'efficiency_min_current_year',
        'efficiency_max_current_year',
        'efficiency_avg_current_year',
        'efficiency_min_last_year',
        'efficiency_max_last_year',
        'efficiency_avg_last_year',
        'failure_rate_current_year',
        'failure_rate_last_year',
        'return_rate_last_year',
        'income_per_mon_last_year',
        'income_per_mon_current_year',
        'cost_per_mon_current_year',
        'cost_per_mon_last_year',
        'return_rate_current_year',
        'benefit_last_year',
        'benefit_current_year',
        'benefit_efficiency_compare',
        'dept',
        'fixcost',
        'varcost',
        'source',
        'equips',
    ];

    protected $tempItemMap = [
        47 => '4.1.40.2.8.01.1710001',
        52 => 'B01',
        54 => 'M02',
        55 => 'M03',
        56 => 'A07',
        57 => 'B05',
        58 => 'M04',
        59 => 'B06',
        60 => 'A08',
        61 => 'A01',
        62 => 'A02',
        63 => 'A03',
        64 => 'A09',
        65 => 'A10',
        66 => '1798231',
        67 => 'Q21323',
        68 => '187463',
        69 => '3253',
        70 => 'test0001',
        71 => 'M01',
        72 => 'CT01',
        73 => 'M03',
        74 => '1',
    ];

    protected function mapCode($eid)
    {
        return $eid;
        // return $this->tempItemMap[$eid];
    }

    /**
     * 本年总收入、本年总成本、本年检查人次：
     * @return array
     */
    public function global_year()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL
        SELECT item_id, sum(total_income) as total_income, sum(total_cost) as total_cost, sum(inspection_times) as inspection_times 
        FROM `item_info_data` 
        WHERE year(date_time) = year(NOW()) 
        AND item_id = ?
        GROUP BY item_id;
SQL;

        $data = Db::query($sql, [$eid]);
        $row = array_pop($data);
        if ($row) {
            return [
                'income' => number_format($row['total_income']),
                'cost' => number_format($row['total_cost']),
                'inspection' => number_format($row['inspection_times'])
            ];
        }

        return [];
    }

    /**
     * 本月总收入、本月总成本、本月检查人次：
     * @return array
     */
    public function global_month()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL
        SELECT item_id, sum(total_income) as total_income, sum(total_cost) as total_cost, sum(inspection_times) as inspection_times 
        FROM `item_info_data` 
        WHERE year(date_time) = year(NOW()) 
        AND month(date_time) = month(NOW()) 
        AND item_id = ?
        GROUP BY item_id;
SQL;

        $data = Db::query($sql, [$eid]);
        $row = array_pop($data);
        if ($row) {
            return [
                'income' => number_format($row['total_income']),
                'cost' => number_format($row['total_cost']),
                'inspection' => number_format($row['inspection_times'])
            ];
        }

        return [];
    }

    public function return_rate()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL
SELECT item_id, return_rate,dur FROM (
SELECT a.item_id, round((a.total_income - a.total_cost) / b.purchase_price, 2) as return_rate,'current' as dur
FROM (SELECT item_id, sum(total_income) as total_income, sum(total_cost) as total_cost
      FROM `item_info_data`
      where year(date_time) = year(NOW())
      GROUP BY item_id) as a
         join think_item as b on a.item_id = b.id and b.`status` = 1 
union all
SELECT a.item_id, round((a.total_income - a.total_cost) / b.purchase_price, 2) as return_rate ,'last' as dur
FROM (SELECT item_id, sum(total_income) as total_income, sum(total_cost) as total_cost
      FROM `item_info_data`
      where year(date_time) = year(NOW()) - 1
      GROUP BY item_id) as a
         join think_item as b on a.item_id = b.id and b.`status` = 1
       ) AS d where d.item_id = ? ;
SQL;


        $data = Db::query($sql, [$eid]);

        $response = [
            'last' => 0,
            'current' => 0
        ];

        foreach ($data as $row) {
            $response[$row['dur']] = $row['return_rate'];
        }
        return $response;
    }

    /**
     * -- 设备故障次数分析（去年）（更新）：
     * @return array
     */
    public function failure_rate_last_year()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL
SELECT c.item_id, c.t_time, if(d.failures_number is null, 0, d.failures_number) as failures_number
FROM (SELECT DISTINCT a.id as item_id, DATE_FORMAT(b.datelist, '%Y-%m') as t_time
      FROM think_item as a,
           think_calendar as b
      where a.`status` = 1
        and year(b.datelist) = year(NOW()) - 1) as c
         LEFT JOIN (SELECT item_id, FROM_UNIXTIME(create_time, '%Y-%m') as t_time, count(1) as failures_number
                    FROM `think_workorder`
                    where FROM_UNIXTIME(create_time, '%Y') = year(NOW()) - 1
                    GROUP BY item_id, FROM_UNIXTIME(create_time, '%Y-%m')) as d
                   on c.item_id = d.item_id and c.t_time = d.t_time
WHERE c.item_id = ?;
SQL;
        $result = Db::query($sql, [$eid]);

        $response = [
            'xAxis' => [],
            'series' => []
        ];
        foreach ($result as $row) {
            $ctime = $row['t_time'];
            $times = $row['failures_number'];
            $response['xAxis'][] = $ctime;
            $response['series'][] = $times;
        }

        return $response;
    }

    /**
     * -- 设备故障次数分析（今年）（更新）：
     * @return array
     */
    public function failure_rate_current_year()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL
SELECT c.item_id, c.t_time, if(d.failures_number is null, 0, d.failures_number) as failures_number
FROM (SELECT DISTINCT a.id as item_id, DATE_FORMAT(b.datelist, '%Y-%m') as t_time
      FROM think_item as a,
           think_calendar as b
      where a.`status` = 1
        and year(b.datelist) = year(NOW())) as c
         LEFT JOIN (SELECT item_id, FROM_UNIXTIME(create_time, '%Y-%m') as t_time, count(1) as failures_number
                    FROM `think_workorder`
                    where FROM_UNIXTIME(create_time, '%Y') = year(NOW())
                    GROUP BY item_id, FROM_UNIXTIME(create_time, '%Y-%m')) as d
                   on c.item_id = d.item_id and c.t_time = d.t_time
WHERE c.item_id = ?;
SQL;
        $result = Db::query($sql, [$eid]);

        $response = [
            'xAxis' => [],
            'series' => []
        ];
        foreach ($result as $row) {
            $ctime = $row['t_time'];
            $times = $row['failures_number'];
            $response['xAxis'][] = $ctime;
            $response['series'][] = $times;
        }

        return $response;
    }

    /**
     * -- 效率分析：
     * -- 设备效率趋势分析（去年）：
     * @return array[]
     */
    public function efficiency_last_year()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL
SELECT item_id, DATE_FORMAT(date_time, '%Y-%m') as t_time, sum(inspection_times) as inspection_times
FROM `item_info_data`
where year(date_time) = year(NOW()) - 1
and item_id = ?
GROUP BY item_id, DATE_FORMAT(date_time, '%Y-%m');
SQL;

        $result = Db::query($sql, [$eid]);

        $response = [
            'xAxis' => [],
            'series' => []
        ];

        foreach ($result as $row) {
            $ctime = $row['t_time'];
            $times = $row['inspection_times'];
            $response['xAxis'][] = $ctime;
            $response['series'][] = $times;
        }

        return $response;
    }

    /**
     * -- 效率分析：
     * -- 设备效率趋势分析（今年）：
     * @return array[]
     */
    public function efficiency_current_year()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL
SELECT item_id, DATE_FORMAT(date_time, '%Y-%m') as t_time, sum(inspection_times) as inspection_times
FROM `item_info_data`
where year(date_time) = year(NOW())
and item_id = ?
GROUP BY item_id, DATE_FORMAT(date_time, '%Y-%m');

SQL;

        $result = Db::query($sql, [$eid]);

        $response = [
            'xAxis' => [],
            'series' => []
        ];

        foreach ($result as $row) {
            $ctime = $row['t_time'];
            $times = $row['inspection_times'];
            $response['xAxis'][] = $ctime;
            $response['series'][] =intval( $times);
        }

        return $response;
    }

    /**
     * -- 月均检查人次、同上期比较（未乘100%）（去年）：
     * @return array[]
     */
    public function efficiency_avg_last_year()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL

SELECT c.item_id,
       c.inspection_times_per,
       case c.inspection_times_per - d.inspection_times_per
           when 0 then 0
           else if(round((c.inspection_times_per - d.inspection_times_per) / d.inspection_times_per, 2) is null, 1,
                   round((c.inspection_times_per - d.inspection_times_per) / d.inspection_times_per,
                         2)) end as inspection_times_per_mom
FROM (SELECT a.item_id, a.t_year, round(avg(a.inspection_times), 0) as inspection_times_per
      FROM (SELECT item_id,
                   year(date_time)                 as t_year,
                   DATE_FORMAT(date_time, '%Y-%m') as t_time,
                   sum(inspection_times)           as inspection_times
            FROM `item_info_data`
            where year(date_time) = year(NOW()) - 1
            GROUP BY item_id, year(date_time), DATE_FORMAT(date_time, '%Y-%m')) as a
      GROUP BY a.item_id, a.t_year) as c
         LEFT JOIN (SELECT b.item_id, b.t_year, round(avg(b.inspection_times), 0) as inspection_times_per
                    FROM (SELECT item_id,
                                 year(date_time)                 as t_year,
                                 DATE_FORMAT(date_time, '%Y-%m') as t_time,
                                 sum(inspection_times)           as inspection_times
                          FROM `item_info_data`
                          where year(date_time) = year(NOW()) - 2
                          GROUP BY item_id, year(date_time), DATE_FORMAT(date_time, '%Y-%m')) as b
                    GROUP BY b.item_id, b.t_year) as d 
         ON c.item_id = d.item_id AND c.t_year - 1 = d.t_year
WHERE c.item_id = ?;
SQL;

        $result = Db::query($sql, [$eid]);

        $response = [
            'inspection_times_per' => [],
            'inspection_times_per_mom' => []
        ];

        foreach ($result as $row) {
            $ctime = $row['inspection_times_per'];
            $times = $row['inspection_times_per_mom'];
            $response['inspection_times_per'] = $ctime;
            $response['inspection_times_per_mom'] = intval( $times);;
        }

        return $response;
    }

    /**
     * -- 月均检查人次、同上期比较（未乘100%）（今年）：
     * @return array[]
     */
    public function efficiency_avg_current_year()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL

SELECT c.item_id,
       c.inspection_times_per,
       case c.inspection_times_per - d.inspection_times_per
           when 0 then 0
           else if(round((c.inspection_times_per - d.inspection_times_per) / d.inspection_times_per, 2) is null, 1,
                   round((c.inspection_times_per - d.inspection_times_per) / d.inspection_times_per,
                         2)) end as inspection_times_per_mom
FROM (SELECT a.item_id, a.t_year, round(avg(a.inspection_times), 0) as inspection_times_per
      FROM (SELECT item_id,
                   year(date_time)                 as t_year,
                   DATE_FORMAT(date_time, '%Y-%m') as t_time,
                   sum(inspection_times)           as inspection_times
            FROM `item_info_data`
            where year(date_time) = year(NOW())
            GROUP BY item_id, year(date_time), DATE_FORMAT(date_time, '%Y-%m')) as a
      GROUP BY a.item_id, a.t_year) as c
         LEFT JOIN (SELECT b.item_id, b.t_year, round(avg(b.inspection_times), 0) as inspection_times_per
                    FROM (SELECT item_id,
                                 year(date_time)                 as t_year,
                                 DATE_FORMAT(date_time, '%Y-%m') as t_time,
                                 sum(inspection_times)           as inspection_times
                          FROM `item_info_data`
                          where year(date_time) = year(NOW()) - 1
                          GROUP BY item_id, year(date_time), DATE_FORMAT(date_time, '%Y-%m')) as b
                    GROUP BY b.item_id, b.t_year) as d 
        ON c.item_id = d.item_id and c.t_year - 1 = d.t_year 
WHERE c.item_id = ?;
SQL;

        $result = Db::query($sql, [$eid]);

        $response = [
            'inspection_times_per' => [],
            'inspection_times_per_mom' => []
        ];

        foreach ($result as $row) {
            $ctime = $row['inspection_times_per'];
            $times = $row['inspection_times_per_mom'];
            $response['inspection_times_per'] = $ctime;
            $response['inspection_times_per_mom'] = $times;
        }

        return $response;
    }

    /**
     * -- -- 单月最大检查人次、同上期比较（未乘100%）（去年）：
     * @return array[]
     */
    public function efficiency_max_last_year()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL

SELECT c.item_id,
       c.inspection_times_max,
       case c.inspection_times_max - d.inspection_times_max
           when 0 then 0
           else if(round((c.inspection_times_max - d.inspection_times_max) / d.inspection_times_max, 2) is null, 1,
                   round((c.inspection_times_max - d.inspection_times_max) / d.inspection_times_max,
                         2)) end as inspection_times_max_mom
FROM (SELECT a.item_id, a.t_year, max(a.inspection_times) as inspection_times_max
      FROM (SELECT item_id,
                   year(date_time)                 as t_year,
                   DATE_FORMAT(date_time, '%Y-%m') as t_time,
                   sum(inspection_times)           as inspection_times
            FROM `item_info_data`
            where year(date_time) = year(NOW()) - 1
            GROUP BY item_id, year(date_time), DATE_FORMAT(date_time, '%Y-%m')) as a
      GROUP BY a.item_id, a.t_year) as c
         left join (SELECT b.item_id, b.t_year, max(b.inspection_times) as inspection_times_max
                    FROM (SELECT item_id,
                                 year(date_time)                 as t_year,
                                 DATE_FORMAT(date_time, '%Y-%m') as t_time,
                                 sum(inspection_times)           as inspection_times
                          FROM `item_info_data`
                          where year(date_time) = year(NOW()) - 2
                          GROUP BY item_id, year(date_time), DATE_FORMAT(date_time, '%Y-%m')) as b
                    GROUP BY b.item_id, b.t_year) as d on c.item_id = d.item_id and c.t_year - 1 = d.t_year 
WHERE c.item_id = ?;
SQL;

        $result = Db::query($sql, [$eid]);

        $response = [
            'inspection_times_max' => [],
            'inspection_times_max_mom' => []
        ];

        foreach ($result as $row) {
            $ctime = $row['inspection_times_max'];
            $times = $row['inspection_times_max_mom'];
            $response['inspection_times_max'] = $ctime;
            $response['inspection_times_max_mom'] = $times;
        }

        return $response;
    }

    /**
     * -- 单月最大检查人次、同上期比较（未乘100%）（今年）：
     * @return array[]
     */
    public function efficiency_max_current_year()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL

SELECT c.item_id,
       c.inspection_times_max,
       case c.inspection_times_max - d.inspection_times_max
           when 0 then 0
           else if(round((c.inspection_times_max - d.inspection_times_max) / d.inspection_times_max, 2) is null, 1,
                   round((c.inspection_times_max - d.inspection_times_max) / d.inspection_times_max,
                         2)) end as inspection_times_max_mom
FROM (SELECT a.item_id, a.t_year, max(a.inspection_times) as inspection_times_max
      FROM (SELECT item_id,
                   year(date_time)                 as t_year,
                   DATE_FORMAT(date_time, '%Y-%m') as t_time,
                   sum(inspection_times)           as inspection_times
            FROM `item_info_data`
            where year(date_time) = year(NOW())
            GROUP BY item_id, year(date_time), DATE_FORMAT(date_time, '%Y-%m')) as a
      GROUP BY a.item_id, a.t_year) as c
         left join (SELECT b.item_id, b.t_year, max(b.inspection_times) as inspection_times_max
                    FROM (SELECT item_id,
                                 year(date_time)                 as t_year,
                                 DATE_FORMAT(date_time, '%Y-%m') as t_time,
                                 sum(inspection_times)           as inspection_times
                          FROM `item_info_data`
                          where year(date_time) = year(NOW()) - 1
                          GROUP BY item_id, year(date_time), DATE_FORMAT(date_time, '%Y-%m')) as b
                    GROUP BY b.item_id, b.t_year) as d on c.item_id = d.item_id and c.t_year - 1 = d.t_year 
WHERE c.item_id = ?;

SQL;

        $result = Db::query($sql, [$eid]);

        $response = [
            'inspection_times_max' => [],
            'inspection_times_max_mom' => []
        ];

        foreach ($result as $row) {
            $ctime = $row['inspection_times_max'];
            $times = $row['inspection_times_max_mom'];
            $response['inspection_times_max'] = $ctime;
            $response['inspection_times_max_mom'] = $times;
        }

        return $response;
    }

    /**
     * -- 单月最小检查人次、同上期比较（未乘100%）（去年）：
     * @return array[]
     */
    public function efficiency_min_last_year()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL


SELECT c.item_id,
       c.inspection_times_min,
       case c.inspection_times_min - d.inspection_times_min
           when 0 then 0
           else if(round((c.inspection_times_min - d.inspection_times_min) / d.inspection_times_min, 2) is null, 1,
                   round((c.inspection_times_min - d.inspection_times_min) / d.inspection_times_min,
                         2)) end as inspection_times_min_mom
FROM (SELECT a.item_id, a.t_year, min(a.inspection_times) as inspection_times_min
      FROM (SELECT item_id,
                   year(date_time)                 as t_year,
                   DATE_FORMAT(date_time, '%Y-%m') as t_time,
                   sum(inspection_times)           as inspection_times
            FROM `item_info_data`
            where year(date_time) = year(NOW()) - 1
            GROUP BY item_id, year(date_time), DATE_FORMAT(date_time, '%Y-%m')) as a
      GROUP BY a.item_id, a.t_year) as c
         left join (SELECT b.item_id, b.t_year, min(b.inspection_times) as inspection_times_min
                    FROM (SELECT item_id,
                                 year(date_time)                 as t_year,
                                 DATE_FORMAT(date_time, '%Y-%m') as t_time,
                                 sum(inspection_times)           as inspection_times
                          FROM `item_info_data`
                          where year(date_time) = year(NOW()) - 2
                          GROUP BY item_id, year(date_time), DATE_FORMAT(date_time, '%Y-%m')) as b
                    GROUP BY b.item_id, b.t_year) as d on c.item_id = d.item_id and c.t_year - 1 = d.t_year 
WHERE c.item_id = ?;

SQL;

        $result = Db::query($sql, [$eid]);

        $response = [
            'inspection_times_min' => [],
            'inspection_times_min_mom' => []
        ];

        foreach ($result as $row) {
            $ctime = $row['inspection_times_min'];
            $times = $row['inspection_times_min_mom'];
            $response['inspection_times_min'] = $ctime;
            $response['inspection_times_min_mom'] = $times;
        }

        return $response;
    }

    /**
     * -- 单月最小检查人次、同上期比较（未乘100%）（今年）：
     * @return array[]
     */
    public function efficiency_min_current_year()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL



SELECT c.item_id,
       c.inspection_times_min,
       case c.inspection_times_min - d.inspection_times_min
           when 0 then 0
           else if(round((c.inspection_times_min - d.inspection_times_min) / d.inspection_times_min, 2) is null, 1,
                   round((c.inspection_times_min - d.inspection_times_min) / d.inspection_times_min,
                         2)) end as inspection_times_min_mom
FROM (SELECT a.item_id, a.t_year, min(a.inspection_times) as inspection_times_min
      FROM (SELECT item_id,
                   year(date_time)                 as t_year,
                   DATE_FORMAT(date_time, '%Y-%m') as t_time,
                   sum(inspection_times)           as inspection_times
            FROM `item_info_data`
            where year(date_time) = year(NOW())
            GROUP BY item_id, year(date_time), DATE_FORMAT(date_time, '%Y-%m')) as a
      GROUP BY a.item_id, a.t_year) as c
         left join (SELECT b.item_id, b.t_year, min(b.inspection_times) as inspection_times_min
                    FROM (SELECT item_id,
                                 year(date_time)                 as t_year,
                                 DATE_FORMAT(date_time, '%Y-%m') as t_time,
                                 sum(inspection_times)           as inspection_times
                          FROM `item_info_data`
                          where year(date_time) = year(NOW()) - 1
                          GROUP BY item_id, year(date_time), DATE_FORMAT(date_time, '%Y-%m')) as b
                    GROUP BY b.item_id, b.t_year) as d on c.item_id = d.item_id and c.t_year - 1 = d.t_year 
WHERE c.item_id = ?;

SQL;

        $result = Db::query($sql, [$eid]);

        $response = [
            'inspection_times_min' => [],
            'inspection_times_min_mom' => []
        ];

        foreach ($result as $row) {
            $ctime = $row['inspection_times_min'];
            $times = $row['inspection_times_min_mom'];
            $response['inspection_times_min'] = $ctime;
            $response['inspection_times_min_mom'] = $times;
        }

        return $response;
    }

    /**
     * -- 效益分析（更新）：
     * --    设备效益趋势分析（去年）（更新）：
     * @return array[]
     */
    public function benefit_last_year()
    {

        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL
   SELECT item_id,
       DATE_FORMAT(date_time, '%Y-%m') as t_time,
       sum(total_income)               as total_income,
       sum(total_cost)                 as total_cost
FROM `item_info_data`
WHERE year(date_time) = year(NOW()) - 1
AND item_id = ?
GROUP BY item_id, DATE_FORMAT(date_time, '%Y-%m');
SQL;

        $result = Db::query($sql, [$eid]);

        $response = [
            'xAxis' => [],
        ];

        foreach ($result as $row) {
            $ctime = $row['t_time'];
            $income = $row['total_income'];
            $cost = $row['total_cost'];
            $response['xAxis'][] = $ctime;
            $response['income'][] = $income;
            $response['cost'][] = $cost;
        }

        return $response;


    }

    /**
     * -- 效益分析（更新）：
     * -- 设备效益趋势分析（今年）（更新）：
     * @return array[]
     */
    public function benefit_current_year()
    {

        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL

SELECT item_id,
       DATE_FORMAT(date_time, '%Y-%m') as t_time,
       sum(total_income)               as total_income,
       sum(total_cost)                 as total_cost
FROM `item_info_data`
WHERE year(date_time) = year(NOW())
AND item_id = ?
GROUP BY item_id, DATE_FORMAT(date_time, '%Y-%m');
SQL;

        $result = Db::query($sql, [$eid]);

        $response = [
            'xAxis' => [],
            'series' => []
        ];

        foreach ($result as $row) {
            $ctime = $row['t_time'];
            $income = $row['total_income'];
            $cost = $row['total_cost'];
            $response['xAxis'][] = $ctime;
            $response['income'][] = $income;
            $response['cost'][] = $cost;
        }

        return $response;
    }

    /**
     * -- 年收益率、同上期比较（未乘100%）（去年）（更新）：
     * @return int[]
     */
    public function return_rate_last_year()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL

SELECT a.item_id,
       round(a.profit_year / c.purchase_price, 2)                                     as return_rate,
       case a.profit_year - b.profit_year
           when 0 then 0
           else if(a.profit_year < 0 and b.profit_year = 0, -1,
                   if(round((a.profit_year - b.profit_year) / b.profit_year, 2) is null, 1,
                      round((a.profit_year - b.profit_year) / b.profit_year, 2))) end as return_rate_mom
FROM (SELECT item_id, sum(total_income) - sum(total_cost) as profit_year
      FROM `item_info_data`
      WHERE year(date_time) = year(NOW()) - 1
      GROUP BY item_id) as a
         LEFT JOIN (SELECT item_id, sum(total_income) - sum(total_cost) as profit_year
                    FROM `item_info_data`
                    WHERE year(date_time) = year(NOW()) - 2
                    GROUP BY item_id) as b on a.item_id = b.item_id
         JOIN think_item as c on a.item_id = c.id and c.`status` = 1
         AND a.item_id = ?;
SQL;

        $response = [
            'return_rate' => 0,
            'return_rate_mom' => 0
        ];

        $data = Db::query($sql, [$eid]);
        $row = array_pop($data);
        if ($row) {
            $response['return_rate'] = $row['return_rate'];
            $response['return_rate_mom'] = $row['return_rate_mom'];
        }

        return $response;
    }

    /**
     * -- 年收益率、同上期比较（未乘100%）（今年）（更新）：
     * @return int[]
     */
    public function return_rate_current_year()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL

SELECT a.item_id,
       round(a.profit_year / c.purchase_price, 2)                                     as return_rate,
       case a.profit_year - b.profit_year
           when 0 then 0
           else if(a.profit_year < 0 and b.profit_year = 0, -1,
                   if(round((a.profit_year - b.profit_year) / b.profit_year, 2) is null, 1,
                      round((a.profit_year - b.profit_year) / b.profit_year, 2))) end as return_rate_mom
FROM (SELECT item_id, sum(total_income) - sum(total_cost) as profit_year
      FROM `item_info_data`
      WHERE year(date_time) = year(NOW())
      GROUP BY item_id) as a
         LEFT JOIN (SELECT item_id, sum(total_income) - sum(total_cost) as profit_year
                    FROM `item_info_data`
                    WHERE year(date_time) = year(NOW()) - 1
                    GROUP BY item_id) as b on a.item_id = b.item_id
         JOIN think_item as c on a.item_id = c.id and c.`status` = 1
         AND a.item_id = ?;
SQL;

        $response = [
            'return_rate' => 0,
            'return_rate_mom' => 0
        ];

        $data = Db::query($sql, [$eid]);
        $row = array_pop($data);
        if ($row) {
            $response['return_rate'] = $row['return_rate'];
            $response['return_rate_mom'] = $row['return_rate_mom'];
        }

        return $response;
    }

    /**
     *-- 月均收入、同上期比较（未乘100%）（去年）（更新）：：
     * @return int[]
     */
    public function income_per_mon_last_year()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL

SELECT c.item_id,
       c.income_per,
       case c.income_per - d.income_per
           when 0 then 0
           else if(round((c.income_per - d.income_per) / d.income_per, 2) is null, 1,
                   round((c.income_per - d.income_per) / d.income_per, 2)) end as income_per_mom
FROM (SELECT a.item_id, round(AVG(a.total_income), 2) income_per
      FROM (SELECT item_id, DATE_FORMAT(date_time, '%Y-%m'), sum(total_income) as total_income
            FROM `item_info_data`
            WHERE year(date_time) = year(NOW()) - 1
            GROUP BY item_id, DATE_FORMAT(date_time, '%Y-%m')) as a
      GROUP BY a.item_id) as c
         LEFT JOIN (SELECT b.item_id, round(AVG(b.total_income), 2) income_per
                    FROM (SELECT item_id, DATE_FORMAT(date_time, '%Y-%m'), sum(total_income) as total_income
                          FROM `item_info_data`
                          WHERE year(date_time) = year(NOW()) - 2
                          GROUP BY item_id, DATE_FORMAT(date_time, '%Y-%m')) as b
                    GROUP BY b.item_id) as d 
                    on c.item_id = d.item_id AND c.item_id = ?; 
SQL;

        $response = [
            'income_per' => 0,
            'income_per_mom' => 0
        ];

        $data = Db::query($sql, [$eid]);
        $row = array_pop($data);
        if ($row) {
            $response['income_per'] = $row['income_per'];
            $response['income_per_mom'] = $row['income_per_mom'];
        }

        return $response;
    }


    /**
     *-- 月均收入、同上期比较（未乘100%）（今年）（更新）：
     * @return int[]
     */
    public function income_per_mon_current_year()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL


SELECT c.item_id,
       c.income_per,
       case c.income_per - d.income_per
           when 0 then 0
           else if(round((c.income_per - d.income_per) / d.income_per, 2) is null, 1,
                   round((c.income_per - d.income_per) / d.income_per, 2)) end as income_per_mom
FROM (SELECT a.item_id, round(AVG(a.total_income), 2) income_per
      FROM (SELECT item_id, DATE_FORMAT(date_time, '%Y-%m'), sum(total_income) as total_income
            FROM `item_info_data`
            WHERE year(date_time) = year(NOW())
            GROUP BY item_id, DATE_FORMAT(date_time, '%Y-%m')) as a
      GROUP BY a.item_id) as c
         LEFT JOIN (SELECT b.item_id, round(AVG(b.total_income), 2) income_per
                    FROM (SELECT item_id, DATE_FORMAT(date_time, '%Y-%m'), sum(total_income) as total_income
                          FROM `item_info_data`
                          WHERE year(date_time) = year(NOW()) - 1
                          GROUP BY item_id, DATE_FORMAT(date_time, '%Y-%m')) as b
                    GROUP BY b.item_id) as d on c.item_id = d.item_id AND c.item_id = ?; 
SQL;

        $response = [
            'income_per' => 0,
            'income_per_mom' => 0
        ];

        $data = Db::query($sql, [$eid]);
        $row = array_pop($data);
        if ($row) {
            $response['income_per'] = $row['income_per'];
            $response['income_per_mom'] = $row['income_per_mom'];
        }

        return $response;
    }

    /**
     *--- 月均成本、同上期比较（未乘100%）（去年）（更新）：
     * @return int[]
     */
    public function cost_per_mon_last_year()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL

SELECT c.item_id,
       c.cost_per,
       case c.cost_per - d.cost_per
           when 0 then 0
           else if(round((c.cost_per - d.cost_per) / d.cost_per, 2) is null, 1,
                   round((c.cost_per - d.cost_per) / d.cost_per, 2)) end as cost_per_mom
FROM (SELECT a.item_id, round(AVG(a.total_cost), 2) cost_per
      FROM (SELECT item_id, DATE_FORMAT(date_time, '%Y-%m'), sum(total_cost) as total_cost
            FROM `item_info_data`
            WHERE year(date_time) = year(NOW()) - 1
            GROUP BY item_id, DATE_FORMAT(date_time, '%Y-%m')) as a
      GROUP BY a.item_id) as c
         LEFT JOIN (SELECT b.item_id, round(AVG(b.total_cost), 2) cost_per
                    FROM (SELECT item_id, DATE_FORMAT(date_time, '%Y-%m'), sum(total_cost) as total_cost
                          FROM `item_info_data`
                          WHERE year(date_time) = year(NOW()) - 2
                          GROUP BY item_id, DATE_FORMAT(date_time, '%Y-%m')) as b
                    GROUP BY b.item_id) as d on c.item_id = d.item_id AND c.item_id = ?; 
SQL;

        $response = [
            'cost_per' => 0,
            'cost_per_mom' => 0
        ];

        $data = Db::query($sql, [$eid]);
        $row = array_pop($data);
        if ($row) {
            $response['cost_per'] = $row['cost_per'];
            $response['cost_per_mom'] = $row['cost_per_mom'];
        }

        return $response;
    }

    /**
     *-- 月均成本、同上期比较（未乘100%）（今年）（更新）：
     * @return int[]
     */
    public function cost_per_mon_current_year()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL


SELECT c.item_id,
       c.cost_per,
       case c.cost_per - d.cost_per
           when 0 then 0
           else if(round((c.cost_per - d.cost_per) / d.cost_per, 2) is null, 1,
                   round((c.cost_per - d.cost_per) / d.cost_per, 2)) end as cost_per_mom
FROM (SELECT a.item_id, round(AVG(a.total_cost), 2) cost_per
      FROM (SELECT item_id, DATE_FORMAT(date_time, '%Y-%m'), sum(total_cost) as total_cost
            FROM `item_info_data`
            WHERE year(date_time) = year(NOW())
            GROUP BY item_id, DATE_FORMAT(date_time, '%Y-%m')) as a
      GROUP BY a.item_id) as c
         LEFT JOIN (SELECT b.item_id, round(AVG(b.total_cost), 2) cost_per
                    FROM (SELECT item_id, DATE_FORMAT(date_time, '%Y-%m'), sum(total_cost) as total_cost
                          FROM `item_info_data`
                          WHERE year(date_time) = year(NOW()) - 1
                          GROUP BY item_id, DATE_FORMAT(date_time, '%Y-%m')) as b
                    GROUP BY b.item_id) as d on c.item_id = d.item_id AND c.item_id = ?;

SQL;

        $response = [
            'cost_per' => 0,
            'cost_per_mom' => 0
        ];

        $data = Db::query($sql, [$eid]);
        $row = array_pop($data);
        if ($row) {
            $response['cost_per'] = $row['cost_per'];
            $response['cost_per_mom'] = $row['cost_per_mom'];
        }

        return $response;
    }

    /**
     *   -- 科室分析（更新）：
     * @return mixed
     */
    public function dept()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
//        var_dump($eid);
//        exit;
        $sql = <<<SQL
      
SELECT item_id, department, COUNT(DISTINCT request_id) as inspection_times, sum(profit) as income
FROM `think_singledia_info`
where year(report_date) = year(NOW())
    and month(report_date) = month(NOW())
    and item_id = ?
GROUP BY item_id, department;

SQL;

        $data = Db::query($sql, [$eid]);

        return
            [
                'code' => 0,
                'data' => $data
            ];
    }

    /**
     * -- 检查来源分析（更新）：
     * @return mixed
     */
    public function source()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL
     
SELECT item_id, patient_source, COUNT(DISTINCT request_id) as inspection_times, sum(profit) as income
FROM `think_singledia_info`
where year(report_date) = year(NOW())
  and month(report_date) = month(NOW())
  and item_id = ?
GROUP BY item_id, patient_source;

SQL;

        $data = Db::query($sql, [$eid]);

        return
            [
                'code' => 0,

                'data' => $data
            ];
    }

    /**
     * -- 本月固定成本（更新）：
     * @return mixed
     */
    public function fixcost()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL
     

SELECT item_id, cost_item, min(date_time) as start_date, max(date_time) as end_date, sum(cost) total_cost
FROM `item_cost_detail`
where year(date_time) = year(NOW())
  and month(date_time) = month(NOW())
  and cost_type = '固定成本'
  and item_id = ?
GROUP BY item_id, cost_item;

SQL;

        $data = Db::query($sql, [$eid]);

        return
            [
                'code' => 0,

                'data' => $data
            ];
    }

    /**
     * -- 本月可变成本（更新）：
     * @return array|mixed
     */
    public function varcost()
    {

        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL
SELECT item_id, cost_item, min(date_time) as start_date, max(date_time) as end_date, sum(cost) total_cost
FROM `item_cost_detail`
where year(date_time) = year(NOW())
  and month(date_time) = month(NOW())
  and cost_type = '可变成本'
  and item_id = ?
GROUP BY item_id, cost_item;

SQL;

        $data = Db::query($sql, [$eid]);

        return
            [
                'code' => 0,

                'data' => $data
            ];
    }

    public function equips()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL
SELECT  tc.id as category_id,tc.name as category,ti.id,ti.code 
FROM think_catagory tc 
INNER JOIN think_item ti
ON tc.id = ti.catagoryid
WHERE tc.status=1 AND ti.status = 1
ORDER BY tc.sort,tc.name,ti.sort

SQL;
        $result = Db::query($sql);

        $response = [];
        foreach ($result as $row) {
            $cid = $row['category_id'];
            $category = $row['category'];
            $eid = $row['id'];
            $code = $row['code'];
            $response["$category-$cid"][] = [
                'id' => $eid,
                'code' => $code
            ];
        }

        return $response;
    }
}
