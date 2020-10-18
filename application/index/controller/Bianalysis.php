<?php


namespace app\index\controller;


use app\admin\common\controller\Base;
use app\index\common\model\Analysis as AnalysisModel;


class Bianalysis extends Base
{
    public function classanalysis()
    {

        $this -> view -> assign('title', '单类设备保障分析');

        return $this -> fetch('classanalysis');
    }

}