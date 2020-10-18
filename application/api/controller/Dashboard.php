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
    protected $checkLoginExclude = ['cur_repair','month_repair','year_contract_cost','year_single_cost','usage_period','dep_period','maintenance_data'];
    const  DEFAULT_EID = 47;


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
    operator.item_cost_detail
WHERE
    cost_item = '设备折旧'
        AND YEAR(date_time) = YEAR(NOW())
    AND item_id = ?
SQL;

        $data = Db::query($sql, [$eid]);
        $row = array_pop($data);
        if ($row) {
            return [
                'total_cost' => number_format($row['total_cost'])
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
            return [
                'total_cost' => number_format($row['total_cost'])
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

}
