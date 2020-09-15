<?php


namespace app\api\controller;


use app\common\controller\Api;
use think\facade\Request;
use app\api\common\model\Qualitylog as QualityLogModel;
use think\facade\Session;

class Qualitylog extends Api
{
    public function confirm() {

        $qcorder["type"] = $this->request->post('type/d');
        $qcorder["qc_status"] = $this->request->post('result/s');
        $qcorder["memo"] = $this->request->post('memo/s');
        $qcorder["location"] = $this->request->post('location/s');
        $qcorder["item_id"] = $this->request->post('id/');
        $qcorder["operator"] = $this->user["id"];
        $qcorder["org_id"] = $this->org["id"];
        QualityLogModel::create($qcorder);
        return json([
            'result' => "质控完成"
        ]);
    }

}