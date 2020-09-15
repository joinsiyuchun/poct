<?php


namespace app\index\controller;


use app\admin\common\controller\Base;

use app\index\common\model\DicomInfo as DicomInfoModel;

use think\facade\Request;



class Dicominfo extends Base
{
    // 接修单管理首页
    public function index()
    {
        $this -> view -> assign('title', 'Dicom信息管理');
        return $this -> view -> fetch('index');
    }

    // 接修单列表
    public function dicominfoList()
    {
        $map = [];
        $wolist= [];
        // 搜索功能
        $keywords = Request::param('keywords');
        if ( !empty($keywords) ) {
            $map[] = ['id', 'like', '%'.$keywords.'%'];
        }

        // 定义分页参数
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        // 获取接修单信息
        $DiagInfoList = DicomInfoModel::where($map)
            -> page($page, $limit)
            -> select();
//        $wolist[]=null;
//        foreach($DiagInfoList as $k=>$v){
//            $wolist[$k]["id"]=$v["id"];
//            $wolist[$k]["deviceId"]=$v["deviceId"];
//            $wolist[$k]["studyInstanceUid"]=$v["studyInstanceUid"];
//            $wolist[$k]["studyId"]=$v["studyId"];
//            $wolist[$k]["patientId"]=$v["patientId"];
//            $wolist[$k]["studyDate"]=$v["studyDate"];
//            $wolist[$k]["studyTime"]=$v["studyTime"];
//            $wolist[$k]["studyDescription"]=$v["studyDescription"];
//            $wolist[$k]["modalitiesInStudy"]=$v["modalitiesInStudy"];
//            $wolist[$k]["accessionNumber"]=$v["accessionNumber"];
//            $wolist[$k]["bodyPartExamined"]=$v["bodyPartExamined"];
//            $wolist[$k]["requestedProcedureDescription"]=$v["requestedProcedureDescription"];
//        }
        $total = count(DicomInfoModel::where($map)->select());
        $result = array("code" => 0, "msg" => "查询成功", "count" => $total, "data" => $DiagInfoList);
        return json($result);
    }



    // 删除接修单
    public function del()
    {
        $id = Request::param('id');

        // 执行删除操作
        try {
            DicomInfoModel::where('id', $id) -> delete();
        } catch (\Exception $e) {
            return resMsg(0, '检查单删除失败' . '<br>' . $e->getMessage(), 'index' );
        }
        return resMsg(1, '检查单删除成功', 'index');
    }


}