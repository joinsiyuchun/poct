<?php
namespace app\api\controller;

use app\common\controller\Api;
use app\api\common\model\Item as ItemModel;
use app\api\common\model\Workorder as WorkorderModel;
use app\api\common\model\Qualitylog as QualitylogModel;
use app\api\common\model\Measurelog as MeasurelogModel;
use think\Db;
use think\facade\Request;

class Dashboard extends Api
{
    protected $checkLoginExclude = ['cur_repair','month_repair','year_contract_cost','year_single_cost','usage_period','dep_period','maintenance_data'
                                    ,'maitenance_rate_cal','maintenance_data_lastyear','maitenance_rate_cal_lastyear','iteminfo','workorder','measure'];
    const  DEFAULT_EID = 47;

    private $contract_cost=0.00;
    private $extra_cost=0.00;
    /**
     * 12个月报修数量、12个月故障停机时长：
     * @return array
     */
    public function cur_repair()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL
SELECT 
    COUNT(id) as total_num, ifnull(SUM(halt_time),0) as total_time
FROM
    think_workorder
WHERE
    create_time > UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 12 MONTH))
    AND item_id = ?
SQL;

        $data = Db::query($sql, [$eid]);
        $row = array_pop($data);
        if ($row) {
            return [
                'total_num' => number_format($row['total_num']),
                'total_time' => number_format($row['total_time'])
            ];
        }

        return [];
    }

    /**
     * 本月报修数量、本月故障停机时长：
     * @return array
     */
    public function month_repair()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL
SELECT 
    COUNT(id) AS total_num,
    IFNULL(SUM(halt_time), 0) AS total_time
FROM
    think_workorder
WHERE
    DATE_FORMAT(from_unixtime(create_time), '%Y%m') = DATE_FORMAT(CURDATE(), '%Y%m')
    AND item_id = ?
SQL;

        $data = Db::query($sql, [$eid]);
        $row = array_pop($data);
        if ($row) {
            return [
                'total_num' => number_format($row['total_num']),
                'total_time' => number_format($row['total_time'])
            ];
        }

        return [];
    }

    /**
     * 本年维修合同折算费用：
     * @return array
     */
    public function year_contract_cost()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL
SELECT 
    SUM(cost) as total_cost
FROM
    item_cost_detail
WHERE
    cost_item = '设备折旧'
        AND YEAR(date_time) = YEAR(NOW())
    AND item_id = ?
SQL;

        $data = Db::query($sql, [$eid]);
        $row = array_pop($data);
        if ($row) {
            $contract_cost = number_format($row['total_cost']);
            return [
                'total_cost' => $contract_cost
            ];
        }

        return [];
    }

    /**
     * 本年保外维修费用：
     * @return array
     */
    public function year_single_cost()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL
SELECT 
    ifnull(SUM(cost),0) as total_cost
FROM
    think_workorder
WHERE
    YEAR(FROM_UNIXTIME(update_time)) = YEAR(NOW())
    AND item_id = ?
SQL;

        $data = Db::query($sql, [$eid]);
        $row = array_pop($data);
        if ($row) {
            $extra_cost=number_format($row['total_cost']);
            return [
                'total_cost' => $extra_cost
            ];
        }

        return [];
    }

    /**
     * 使用年限：
     * @return array
     */
    public function usage_period()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL
SELECT 
    TIMESTAMPDIFF(month,start_date,now()) as month_gap
FROM
    think_item
where id = ?
SQL;

        $data = Db::query($sql, [$eid]);
        $row = array_pop($data);
        if ($row) {
            return [
                'month_gap' => number_format($row['month_gap'])
            ];
        }

        return [];
    }

    /**
     * 折旧年限：
     * @return array
     */
    public function dep_period()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL
SELECT 
    b.depreciation*12 as depreciation
FROM
    think_item a,
    think_catagory b
where a.catagoryid=b.id
and a.id = ?
SQL;

        $data = Db::query($sql, [$eid]);
        $row = array_pop($data);
        if ($row) {
            return [
                'depreciation_period' => number_format($row['depreciation'])
            ];
        }

        return [];
    }

       /**
     * 雷达图：设备保障综合分析
     * @return array
     */
    public function maintenance_data()
    {
        $response = [];

        $eid = Request::param('id', self::DEFAULT_EID);
        $item=ItemModel::find($eid);
        $code=$item['code'];
        $start_time=strtotime($item["start_date"]);
        $catagory=$item->catagory()->find();
        $catagory_id=$catagory['id'];

        //single equip
        $single_fault_count=WorkorderModel::where('item_id',$eid)->whereTime('report_time','year')->count();
        $single_pm_count=QualitylogModel::where(['item_id'=>$eid,'type'=>3])->whereTime('qc_time','year')->count();
        $single_measure_count=MeasurelogModel::where(['item_id'=>$eid,'is_measure'=>1])->whereTime('qc_time','year')->count();
        $single_force_count=MeasurelogModel::where(['item_id'=>$eid,'type'=>2])->whereTime('qc_time','year')->count();

        $single_usage_period=$this->usage_month($eid);
        //avg
        $avg_fault_count=$this->avg_fault_count($catagory_id);
        $avg_pm_count=$this->avg_pm_count($catagory_id);
        $avg_measure_count=$this->avg_measure_count($catagory_id);
        $avg_force_count=$this->avg_force_count($catagory_id);
        $avg_usage_period=$this->avg_usage_month($catagory_id);
        $response[0]['name'] = "同类台均";
        $response[0]['value'] = [$avg_fault_count, $avg_pm_count, $avg_measure_count, $avg_force_count, $avg_usage_period];
        $response[1]['name'] = $catagory['name'].'-'.$code;
        $response[1]['value'] = [$single_fault_count, $single_pm_count, $single_measure_count, $single_force_count, $single_usage_period];

        return $response;

    }


    /**
     * 雷达图：去年设备保障综合分析
     * @return array
     */
    public function maintenance_data_lastyear()
    {
        $response = [];

        $eid = Request::param('id', self::DEFAULT_EID);
        $item=ItemModel::find($eid);
        $code=$item['code'];
        $start_time=strtotime($item["start_date"]);
        $catagory=$item->catagory()->find();
        $catagory_id=$catagory['id'];

        //single equip
        $single_fault_count=WorkorderModel::where('item_id',$eid)->whereTime('report_time','last year')->count();
        $single_pm_count=QualitylogModel::where(['item_id'=>$eid,'type'=>3])->whereTime('qc_time','last year')->count();
        $single_measure_count=MeasurelogModel::where(['item_id'=>$eid,'is_measure'=>1])->whereTime('qc_time','last year')->count();
        $single_force_count=MeasurelogModel::where(['item_id'=>$eid,'type'=>2])->whereTime('qc_time','last year')->count();
        $single_usage_period=$this->usage_month_lastyear($eid);
        //avg
        $avg_fault_count=$this->avg_fault_count_lastyear($catagory_id);
        $avg_pm_count=$this->avg_pm_count_lastyear($catagory_id);
        $avg_measure_count=$this->avg_measure_count_lastyear($catagory_id);
        $avg_force_count=$this->avg_force_count_lastyear($catagory_id);
        $avg_usage_period=$this->avg_usage_month_lastyear($catagory_id);
        $response[0]['name'] = "同类台均";
        $response[0]['value'] = [$avg_fault_count, $avg_pm_count, $avg_measure_count, $avg_force_count, $avg_usage_period];
        $response[1]['name'] = $catagory['name'].'-'.$code;
        $response[1]['value'] = [$single_fault_count, $single_pm_count, $single_measure_count, $single_force_count, $single_usage_period];

        return $response;

    }



    /**
     * 雷达图：单类设备本年故障次数
     * @return array
     */
    private function avg_fault_count($catagory_id){
        $sql = <<<SQL
SELECT 
    COUNT(a.id) as total
FROM
    think_workorder a,
    think_item b
WHERE
    a.item_id = b.id 
    AND year(report_time)=year(now())
    AND b.catagoryid = ?
SQL;

        $data = Db::query($sql, [$catagory_id]);
        $row = array_pop($data);
        $total = ItemModel::where(['status'=>1])->count();
        if ($row) {
            $count=number_format($row['total']);
            return number_format($count/$total,2);
        }
        return 0.0;
    }

    /**
     * 雷达图：去年单类设备本年故障次数
     * @return array
     */
    private function avg_fault_count_lastyear($catagory_id){
        $sql = <<<SQL
SELECT 
    COUNT(a.id) as total
FROM
    think_workorder a,
    think_item b
WHERE
    a.item_id = b.id 
    AND year(report_time)=year(now())-1
    AND b.catagoryid = ?
SQL;

        $data = Db::query($sql, [$catagory_id]);
        $row = array_pop($data);
        $year=date("Y",time());
        $total = ItemModel::where(['status'=>1])->whereTime('start_date','<',$year.'-01-01')->count();
        if ($row) {
            $count=number_format($row['total']);
            return number_format($count/$total,2);
        }
        return 0.0;
    }

    /**
     * 雷达图：单类设备本年PM次数
     * @return array
     */
    private function avg_pm_count($catagory_id){
        $sql = <<<SQL
SELECT 
    COUNT(a.id) as total
FROM
    think_quality_log a,
    think_item b
WHERE
    a.item_id = b.id 
    AND year(qc_time)=year(now())
     and a.type= 3
    AND b.catagoryid = ?
SQL;

        $data = Db::query($sql, [$catagory_id]);
        $row = array_pop($data);
        $total = ItemModel::where(['status'=>1])->count();
        if ($row) {
            $count=number_format($row['total']);
            return number_format($count/$total,2);
        }
        return 0.0;
    }

    /**
     * 雷达图：去年单类设备本年PM次数
     * @return array
     */
    private function avg_pm_count_lastyear($catagory_id){
        $sql = <<<SQL
SELECT 
    COUNT(a.id) as total
FROM
    think_quality_log a,
    think_item b
WHERE
    a.item_id = b.id 
    AND year(qc_time)=year(now())-1
     and a.type= 3
    AND b.catagoryid = ?
SQL;

        $data = Db::query($sql, [$catagory_id]);
        $row = array_pop($data);
        $year=date("Y",time());
        $total = ItemModel::where(['status'=>1])->whereTime('start_date','<',$year.'-01-01')->count();
        if ($row) {
            $count=number_format($row['total']);
            return number_format($count/$total,2);
        }
        return 0.0;
    }

    /**
 * 雷达图：单类设备本年计量次数
 * @return array
 */
    private function avg_measure_count($catagory_id){
        $sql = <<<SQL
SELECT 
    COUNT(a.id) as total
FROM
    think_measure_log a,
    think_item b
WHERE
    a.item_id = b.id 
    AND year(qc_time)=year(now())
    and a.is_measure= 1
    AND b.catagoryid = ?
SQL;

        $data = Db::query($sql, [$catagory_id]);
        $row = array_pop($data);
        $total = ItemModel::where(['status'=>1])->count();
        if ($row) {
            $count=number_format($row['total']);
            return number_format($count/$total,2);
        }
        return 0.0;
    }

    /**
     * 雷达图：去年单类设备本年计量次数
     * @return array
     */
    private function avg_measure_count_lastyear($catagory_id){
        $sql = <<<SQL
SELECT 
    COUNT(a.id) as total
FROM
    think_measure_log a,
    think_item b
WHERE
    a.item_id = b.id 
    AND year(qc_time)=year(now())-1
    and a.is_measure= 1
    AND b.catagoryid = ?
SQL;

        $data = Db::query($sql, [$catagory_id]);
        $row = array_pop($data);
        $year=date("Y",time());
        $total = ItemModel::where(['status'=>1])->whereTime('start_date','<',$year.'-01-01')->count();
        if ($row) {
            $count=number_format($row['total']);
            return number_format($count/$total,2);
        }
        return 0.0;
    }

    /**
     * 雷达图：单类设备本年计量次数
     * @return array
     */
    private function avg_force_count($catagory_id){
        $sql = <<<SQL
SELECT 
    COUNT(a.id) as total
FROM
    think_measure_log a,
    think_item b
WHERE
    a.item_id = b.id 
    AND year(qc_time)=year(now())
    and a.type= 1
    AND b.catagoryid = ?
SQL;

        $data = Db::query($sql, [$catagory_id]);
        $row = array_pop($data);
        $total = ItemModel::where(['status'=>1])->count();
        if ($row) {
            $count=number_format($row['total']);
            return number_format($count/$total,2);
        }
        return 0.0;
    }

    /**
     * 雷达图：去年单类设备本年计量次数
     * @return array
     */
    private function avg_force_count_lastyear($catagory_id){
        $sql = <<<SQL
SELECT 
    COUNT(a.id) as total
FROM
    think_measure_log a,
    think_item b
WHERE
    a.item_id = b.id 
    AND year(qc_time)=year(now())-1
    and a.type= 1
    AND b.catagoryid = ?
SQL;

        $data = Db::query($sql, [$catagory_id]);
        $row = array_pop($data);
        $year=date("Y",time());
        $total = ItemModel::where(['status'=>1])->whereTime('start_date','<',$year.'-01-01')->count();
        if ($row) {
            $count=number_format($row['total']);
            return number_format($count/$total,2);
        }
        return 0.0;
    }

    /**
     * 单台设备使用年限：
     * @return array
     */
    public function usage_month($equip_id)
    {
        $sql = <<<SQL
SELECT 
    TIMESTAMPDIFF(month,start_date,now()) as month_gap
FROM
    think_item
where id = ?
SQL;

        $data = Db::query($sql, [$equip_id]);
        $row = array_pop($data);
        if ($row) {
            return  number_format($row['month_gap']/12,2);
        }

        return 0.0;
    }

    /**
     * 去年单台设备使用年限：
     * @return array
     */
    public function usage_month_lastyear($equip_id)
    {
        $sql = <<<SQL
SELECT 
    TIMESTAMPDIFF(month,start_date,now()) as month_gap
FROM
    think_item
where id = ?
SQL;

        $data = Db::query($sql, [$equip_id]);
        $row = array_pop($data);
        if ($row) {
            $duration=number_format($row['month_gap']/12,2);
            $d=getdate();
            if($duration>=$d['mon']){
                $result=$duration-$d['mon'];
            }else{
                $result=0.00;
            }
            return  $result;
        }

        return 0.0;
    }




    /**
     * 雷达图：单类设备使用年限
     * @return array
     */
    private function avg_usage_month($catagory_id){
        $sql = <<<SQL
SELECT 
    sum(TIMESTAMPDIFF(month,start_date,now())) as month_gap
FROM
    think_item
where catagoryid = ?
SQL;

        $data = Db::query($sql, [$catagory_id]);
        $row = array_pop($data);
        $total = ItemModel::where(['status'=>1])->count();
        if ($row) {
            $count=number_format($row['month_gap']);
            return number_format($count/12/$total,2);
        }
        return 0.0;
    }

    /**
     * 雷达图：去年单类设备使用年限
     * @return array
     */
    private function avg_usage_month_lastyear($catagory_id){
        $sql = <<<SQL
SELECT 
    sum(TIMESTAMPDIFF(month,start_date,now())) as month_gap
FROM
    think_item
where catagoryid = ?
SQL;

        $data = Db::query($sql, [$catagory_id]);
        $row = array_pop($data);
        $year=date("Y",time());
        $total = ItemModel::where(['status'=>1])->whereTime('start_date','<',$year.'-01-01')->count();
        if ($row) {
            $duration=number_format($row['month_gap']/12,2);
            $d=getdate();
            if($duration>=$d['mon']){
                $result=$duration-$d['mon'];
            }else{
                $result=0.00;
            }
            return  $result/$total;
        }
        return 0.0;
    }
//    /**
//     * 雷达图：同类设备故障次数比
//     * @return array
//     */
//    public function faultrate()
//    {
//        $org_id=$this->org["id"];
//        $total = ItemModel::where(['is_backup'=>0,'status'=>1])->count();
//        $fault=WorkorderModel::where(['org_id'=>$org_id])->where('status','in','3,4')->count();
//        if($total==0){
//            $rate=0;
//        }else{
//            $rate=round($fault/$total,2)*100;
//        }
//        $fault_rate["rate"]=$rate;
//        return json($fault_rate);
//    }

    /**
     * -- 故障率、可用率、维修费用率：
     * @return int[]
     */
    public function maitenance_rate_cal()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL
SELECT 
    ifnull(round(SUM(halt_time)/(dayofyear(now())*24),2),0.00) as fault_rate
FROM
    think_workorder
WHERE
    YEAR(report_time) = YEAR(NOW())
         AND item_id = ?
SQL;
        $data = Db::query($sql, [$eid]);
        $row = array_pop($data);
        $item=ItemModel::find($eid);
        $purchase_price=$item['purchase_price'];
        $cost_rate=0.00;
        if($purchase_price>0){
            $cost_rate=($this->contract_cost+$this->extra_cost)/$purchase_price;
        }
        if ($row) {
            $fault_rate=$row['fault_rate']*100;
            $usage_rate=(1-$fault_rate)*100;
            $response = [
                'fault_rate' => number_format($fault_rate,2),
                'usage_rate' => number_format($usage_rate,2),
                'cost_rate' => number_format($cost_rate,2)
            ];
            return $response;
        }
        $response = [
            'fault_rate' => 0.00,
            'usage_rate' => 0.00,
            'cost_rate' => 0.00
        ];


        return $response;
    }

    /**
     * -- 去年故障率、可用率、维修费用率：
     * @return int[]
     */
    public function maitenance_rate_cal_lastyear()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL
SELECT 
    ifnull(round(SUM(halt_time)/(dayofyear(now())*24),2),0.00) as fault_rate
FROM
    think_workorder
WHERE
    YEAR(report_time) = YEAR(NOW())-1
         AND item_id = ?
SQL;
        $data = Db::query($sql, [$eid]);
        $row = array_pop($data);
        $item=ItemModel::find($eid);
        $purchase_price=$item['purchase_price'];
        $cost_rate=0.00;
        if($purchase_price>0){
            $cost_rate=($this->contract_cost+$this->extra_cost)/$purchase_price;
        }
        if ($row) {
            $fault_rate=$row['fault_rate']*100;
            $usage_rate=(1-$fault_rate)*100;
            $response = [
                'fault_rate' => number_format($fault_rate,2),
                'usage_rate' => number_format($usage_rate,2),
                'cost_rate' => number_format($cost_rate,2)
            ];
            return $response;
        }
        $response = [
            'fault_rate' => 0.00,
            'usage_rate' => 0.00,
            'cost_rate' => 0.00
        ];


        return $response;
    }

    /**
     *   -- 设备信息：
     * @return mixed
     */
    public function iteminfo()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL
      
SELECT 
    a.id as item_id, a.code, b.name, a.start_date, round(a.purchase_price,2) as purchase_price,a.brand,a.model
FROM
    think_item a,
    think_catagory b
WHERE
    a.catagoryid = b.id
    and a.id = ?
SQL;

        $data = Db::query($sql, [$eid]);

        return
            [
                'code' => 0,
                'data' => $data
            ];
    }

    /**
     *   -- 维修信息：
     * @return mixed
     */
    public function workorder()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL
      
SELECT 
    b.code,
    a.report_time,
    case a.status when 0 then '待接修' when 1 then '维修中' when 2 then '已完修' when 3 then '已结算' end as status,
    case a.is_halt when 1 then '故障停机' else '未停机' end as is_halt,
    a.halt_time,
    c.user_name,
    a.complete_time
FROM
    think_workorder a,
    think_notification b,
    think_user c
WHERE
    a.notification_id = b.id
	and a.receptor_id=c.id
    and a.item_id = ?
SQL;

        $data = Db::query($sql, [$eid]);

        return
            [
                'code' => 0,
                'data' => $data
            ];
    }

    /**
     *   -- 质控信息：
     * @return mixed
     */
    public function measure()
    {
        $eid = Request::param('id', self::DEFAULT_EID);
        $sql = <<<SQL
SELECT 
    b.user_name,
    CASE qc_status
        WHEN 1 THEN '部分通过'
        WHEN 0 THEN '未通过'
        WHEN 2 THEN '完全通过'
    END AS result,
    CASE type
        WHEN 1 THEN '每日巡检'
        WHEN 2 THEN '每月巡检'
        WHEN 3 THEN '预防性维护'
    END AS type,
    qc_time,
    '非计量' AS is_measure
FROM
    think_quality_log a,
    think_user b
WHERE
    a.operator = b.id 
    and a.item_id = ?
UNION SELECT 
    b.user_name,
    CASE qc_status
        WHEN 1 THEN '部分通过'
        WHEN 0 THEN '未通过'
        WHEN 2 THEN '完全通过'
    END AS result,
    CASE type
        WHEN 1 THEN '强检'
        WHEN 2 THEN '非强检查'
    END AS type,
    qc_time,
    CASE is_measure
        WHEN 0 THEN '非计量'
        WHEN 1 THEN '计量'
    END AS is_measure
FROM
    think_measure_log a,
    think_user b
WHERE
    a.operator = b.id
    and a.item_id = ?
SQL;

        $data = Db::query($sql, [$eid,$eid]);

        return
            [
                'code' => 0,
                'data' => $data
            ];
    }

}
