<?php


namespace app\api\controller;


use app\common\controller\Api;
use think\facade\Request;
use app\api\common\model\DicomInfo as DicomInfoModel;
use think\facade\Session;

class Dicom extends Api
{
    public function store() {

        $dicominfo["deviceId"] = $this->request->post('deviceId/s');
        $dicominfo["studyInstanceUid"] = $this->request->post('studyInstanceUid/s');
        $dicominfo["studyId"] = $this->request->post('studyId/s');
        $dicominfo["patientId"] = $this->request->post('patientId/s');
        $dicominfo["studyTime"] = $this->request->post('studyTime/s');
        $dicominfo["studyDate"] = $this->request->post('studyDate/s');
        $dicominfo["studyDescription"] = $this->request->post('studyDescription/s');
        $dicominfo["modalitiesInStudy"] = $this->request->post('modalitiesInStudy/s');
        $dicominfo["accessionNumber"] = $this->request->post('accessionNumber/s');
        $dicominfo["bodyPartExamined"] = $this->request->post('bodyPartExamined/s');
        $dicominfo["requestedProcedureDescription"] = $this->request->post('requestedProcedureDescription/s');
        DicomInfoModel::create($dicominfo);
        return json([
            'result' => "success"
        ]);
    }

}