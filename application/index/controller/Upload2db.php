<?php


namespace app\index\controller;

use think\Controller;
use think\Db;
use think\facade\Request;
use think\facade\Session;

//use \PHPExcel\PHPExcel\Reader\Excel5;


class Upload2db extends Controller
{

    public $excelConfig = [
        'bireport' => [
            'columns' => [
                'A' => 'code',
                'B' => 'pid',
                'C' => 'sort',
                'D' => 'catagoryid',
                'E' => 'is_kit',
                'F' => 'status',
                'G' => 'brand',
                'H' => 'model',
                'I' => 'sn',
                'J' => 'is_backup',
                'K' => 'location',
                'L' => 'purchase_price',
                'M' => 'start_date'
            ],
            'table' => 'think_item',
            'title' => '选择设备档案文件'
        ]
    ];

    // 接修单管理首页
    public function index()
    {
        $type = Request::param('type');
        if (!$this->excelConfig[$type]) {
            $this->view->assign('title', '上传');
            $this->view->assign('type', $type);
            $this->view->assign('msg', "上传数据模板没有配置");
        } else {
            $config = $this->excelConfig[$type];
            $this->view->assign('title', $config['title']);
            $this->view->assign('type', $type);
            $this->view->assign('msg', '');
        }
        return $this->view->fetch('uploader');
    }

    /**
     * Created by PhpStorm.
     * function: data_import
     * Description:导入数据
     * User: Xiaoxie
     * @param $filename
     * @param string $exts
     * @param $or
     *
     */
    public function data_import($filename, $exts = 'xls', $or)
    {


        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
        vendor('PHPExcel.PHPExcel');
        //创建PHPExcel对象，注意，不能少了\
        $PHPExcel = new \PHPExcel();
        //如果excel文件后缀名为.xls，导入这个类
        if ($exts == 'xls') {
            Vendor('PHPExcel.PHPExcel.Reader.Excel5');
            $PHPReader = new \PHPExcel_Reader_Excel5();
        } else if ($exts == 'xlsx') {
            Vendor('PHPExcel.PHPExcel.Reader.Excel2007');
            $PHPReader = new \PHPExcel_Reader_Excel2007();
        }


        //载入文件
        $PHPExcel = $PHPReader->load($filename);
        //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
        $currentSheet = $PHPExcel->getSheet(0);
        //获取总列数
        $allColumn = $currentSheet->getHighestColumn();
        //获取总行数
        $allRow = $currentSheet->getHighestRow();
        //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
        for ($currentRow = 1; $currentRow <= $allRow; $currentRow++) {
            //从哪列开始，A表示第一列
            for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
                //数据坐标
                $address = $currentColumn . $currentRow;
                //读取到的数据，保存到数组$data中
                $cell = $currentSheet->getCell($address)->getValue();

                if ($cell instanceof PHPExcel_RichText) {
                    $cell = $cell->__toString();
                }
                $data[$currentRow - 1][$currentColumn] = $cell;
                //  print_r($cell);
            }

        }
        // 写入数据库操作
        $this->insert_data($data);

    }

    /**
     * Created by PhpStorm.
     * function: imports
     * Description:导入excell
     * User: Xiaoxie
     *
     */
    public function imports()
    {
        header("content-type:text/html;charset=utf-8");
        //上传excel文件

        $file = Request::file('myfile');
        $ignore = Request::param('ignoreHeader');
        $type = Request::param('type');
        if (!$this->excelConfig[$type]) {
            $type = Request::param('type');
            $this->view->assign('title', '上传测试');
            $this->view->assign('type', $type);
            $this->view->assign('msg', "上传数据模板没有配置");
            return $this->view->fetch('uploader');
        }
        //移到/uploads/excel/下
        $info = $file->move(ROOT_PATH . '/uploads/excel');
        //上传文件成功
        if ($info) {
            //引入PHPExcel类
            // \vendor('PHPExcel.PHPExcel.Reader.Excel5');
            //获取上传后的文件名
            $fileName = $info->getSaveName();
            //文件路径
            $filePath = 'uploads/excel/' . $fileName;
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $PHPReader = null;
            $objPHPExcel = null;
            if ($extension == 'xlsx') {
                $PHPReader = new \PHPExcel_Reader_Excel2007();
            } else if ($extension == 'xls') {
                $PHPReader = new \PHPExcel_Reader_Excel5();
            } else if ($extension == 'csv') {
                $PHPReader = new \PHPExcel_Reader_CSV();
                //默认输入字符集
                $PHPReader->setInputEncoding('UTF-8');
                //默认的分隔符
                $PHPReader->setDelimiter(',');
            }

            $objPHPExcel = $PHPReader->load($filePath);
            //读取excel文件中的第一个工作表
            $sheet = $objPHPExcel->getSheet(0);
            $allRow = $sheet->getHighestRow();  //取得总行数
            //$allColumn = $sheet->getHighestColumn();  //取得总列数
            //从第二行开始插入，第一行是列名
            $config = $this->excelConfig[$type];
            $data = [];
            $j = $ignore ? 2 : 1;
            for ($j; $j <= $allRow; $j++) {
                $k = $ignore ? $j - 2 : $j - 1;
                foreach ($config ['columns'] as $key => $name) {
                    $data[$k][$name] = $objPHPExcel->getActiveSheet()->getCell($key . $j)->getValue();
                }
            }
            Db::table($config['table'])->insertAll($data);

            $type = Request::param('type');
            $this->view->assign('title', '上传测试');
            $this->view->assign('type', $type);
            $this->view->assign('msg', "导入成功");
            return $this->view->fetch('uploader');
        }
    }
}
