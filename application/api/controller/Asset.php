<?php

namespace app\api\controller;


use app\api\common\model\Item as ItemModel;
use app\common\controller\Api;
use app\api\common\model\DiagInfo as DiagInfoModel;
use think\Db;
use think\facade\Request;


class Asset extends API
{

    protected $checkLoginExclude = ['item_tendency','ranking_department','equipment_percent','profit_rate','equipment_list','source_list'];

    /**
     * 设备类型，各类型数量、各类型停机数量：
     * @return array
     */
    public function item_tendency()
    {
        $sql = <<<SQL
SELECT 
    item_name as name, SUM(profit)/10000 as total
FROM
    think_singledia_info
where year(inspection_date)=year(now())
GROUP BY item_name
order by total desc
SQL;
        $result = Db::query($sql);
        $response = [];
        foreach ($result as $k=>$row) {
            $i=(double)$row['total'];
            $response[$k]['title']=$row['name'];
            $response[$k]['unit']='万元/本年';
            $response[$k]['number']['number'][0]=$i;
            $response[$k]['number']['content']='{nt}';
            $response[$k]['number']['textAlign']='center';
            $response[$k]['number']['toFixed']=2;
            $response[$k]['number']['style']['fill']='#4d99fc';
            $response[$k]['number']['style']['fontWeight']='bold';
        }
        return json($response);
    }

    /**
     * 各部门收入：
     * @return array
     */
    public function ranking_department()
    {
        $sql = <<<SQL
SELECT 
    department as name, SUM(profit)/10000 as total
FROM
    think_singledia_info
where year(inspection_date)=year(now())
GROUP BY department
order by total desc
limit 10
SQL;
        $result = Db::query($sql);
        $response = [];
        foreach ($result as $k=>$row) {
            $i=(double)$row['total'];
            $response[$k]['name']=$row['name'];
            $response[$k]['value']=$i;
        }
        return json($response);
    }

    /**
     * 设备收入分布饼图：
     * @return array
     */
    public function equipment_percent()
    {
        $sql = <<<SQL
SELECT 
    item_name as name, SUM(profit) as total
FROM
    think_singledia_info
GROUP BY item_name
ORDER BY SUM(profit) DESC
LIMIT 10;
SQL;
        $result = Db::query($sql);
        $response = [];
        foreach ($result as $k=>$row) {
            $i=(double)$row['total'];
            $response[$k]['name']=$row['name'];
            $response[$k]['value']=$i;
        }
        return json($response);
    }

    /**
     * 设备利润率：
     * @return array
     */
    public function profit_rate()
    {
        $sql = <<<SQL
SELECT 
    ROUND(SUM(total_income) / 10000, 2) AS profit,
    ROUND(SUM(total_cost) / 10000, 2) AS cost,
    ROUND((SUM(total_income) - SUM(total_cost)) / SUM(total_income),
            2) * 100 AS rate
FROM
    item_info_data
WHERE
    YEAR(date_time) = YEAR(NOW());
SQL;
        $result = Db::query($sql);
        $response = [];
//        $i=(double)$result["rate"];
        $response["profit"]=$result[0]["profit"];
        $response["cost"]=$result[0]["cost"];
        $response["rate"][0]=$result[0]["rate"];
        return json($response);
    }

    /**
     * 设备收入成本列表：
     * @return array
     */
    public function equipment_list()
    {
        $sql = <<<SQL
SELECT 
    a.item_id,b.code as code,
    CONCAT(b.name, '-', b.brand, '-', b.model) as equip_name,
    ROUND(SUM(a.total_income), 2) as income,
    ROUND(SUM(a.total_cost), 2) as cost
FROM
    item_info_data AS a
        LEFT JOIN
    (SELECT 
        items.id,items.code,
            IFNULL(catagory.name, '') AS name,
            IFNULL(items.brand, '') AS brand,
            IFNULL(items.model, '') AS model
    FROM
        think_item AS items, think_catagory AS catagory
    WHERE
        items.catagoryid = catagory.id
    ORDER BY catagory.name) AS b ON a.item_id = b.id
GROUP BY a.item_id
order by income desc;
SQL;
        $result = Db::query($sql);
        $response = [];
        foreach ($result as $k=>$row) {
            $response[$k][0]=$row['code'];
            $response[$k][1]=$row['equip_name'];
            $response[$k][2]=$row['income'];
            $response[$k][3]=$row['cost'];
        }
        return json($response);
    }

    /**
     * 根据患者来源计算收入成本、检查人次：
     * @return array
     */
    public function source_list()
    {
        $sql = <<<SQL
-- SELECT 
--     patient_source as source, round(SUM(profit)/10000,2) as total_profit,count(1) total_times
-- FROM
--     think_singledia_info
-- WHERE
--     YEAR(inspection_date) = YEAR(NOW())
-- GROUP BY patient_source
SELECT 
    source, SUM(total_amount) as total_profit,sum(total_count) as total_times
FROM
    patient_source_ananlysis
where year = year(now())
GROUP BY source;
SQL;
        $result = Db::query($sql);
        $all_counts=0;
        foreach ($result as $row) {
            $all_counts = $all_counts+$row['total_times'];
        }
        $response = [];
        foreach ($result as $k=>$row) {
            $i=(double)$row['total_profit'];
            $total=$row['total_times'];
            $percent=$total/$all_counts*100;
            $response[$k]['title']=$row['source'];
            $response[$k]['total']['number'][0]=$i;
            $response[$k]['total']['content']='{nt}';
            $response[$k]['total']['textAlign']='center';
            $response[$k]['total']['toFixed']=2;
            $response[$k]['total']['style']['fill']='#ea6027';
            $response[$k]['total']['style']['fontWeight']='bold';
            $response[$k]['num']['number'][0]=$total;
            $response[$k]['num']['content']='{nt}';
            $response[$k]['num']['textAlign']='center';
            $response[$k]['num']['toFixed']=0;
            $response[$k]['num']['style']['fill']='#26fcd8';
            $response[$k]['num']['style']['fontWeight']='bold';

            $response[$k]['ring']['series'][0]['type']='gauge';
            $response[$k]['ring']['series'][0]['startAngle']=-2.57;
            $response[$k]['ring']['series'][0]['endAngle']=4.71;
            $response[$k]['ring']['series'][0]['arcLineWidth']=13;
            $response[$k]['ring']['series'][0]['radius']='80%';
            $response[$k]['ring']['series'][0]['data'][0]['name']='检查人次占比';
            $response[$k]['ring']['series'][0]['data'][0]['value']=$percent;
            $response[$k]['ring']['series'][0]['axisLabel']['show']=false;
            $response[$k]['ring']['series'][0]['axisTick']['show']=false;
            $response[$k]['ring']['series'][0]['pointer']['show']=false;
            $response[$k]['ring']['series'][0]['backgroundArc']['style']['stroke']='#224590';
            $response[$k]['ring']['series'][0]['details']['show']=true;
            $response[$k]['ring']['series'][0]['details']['formatter']='{value}%';
            $response[$k]['ring']['series'][0]['details']['show']=true;
            $response[$k]['ring']['series'][0]['details']['style']['fill']='#26fcd8';
            $response[$k]['ring']['series'][0]['details']['style']['fontWeight']='bold';

            $response[$k]['ring']['color'][0]='#03d3ec';
        }
        return json($response);
    }

}
