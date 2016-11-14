<?php

namespace App\Http\Controllers;

use PHPExcel_Worksheet;
use App\Model\Room;
use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{

    public function __construct()
    {
        $this->middleware('my.auth');
    }

    /**
     * 导出文件
     * @param $filename
     * @param $data
     */
    public static function exportFile($filename, $data)
    {
        Excel::create($filename, function($excel) use($data) {
            $excel->sheet('公司明细', function($sheet) use($data){
                $chars = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
                $columnChar = $chars[count($data[1]) - 1];
                $sheet->setAutoSize(true);
                $sheet->setRowsToRepeatAtTop(['1','2']);
                //设置表样式
                $sheet->setPageMargin(array(
                    0.4, 0.4, 0.4, 0.4
                ));
                $sheet->setStyle(array(
                    'font' => array(
                        'name'      =>  '宋体',
                        'size'      =>  11,
                    )
                ));
                $sheet->cell('A1', function($cell) {
                    $cell->setFont(array(
                        'size'       => '16',
                        'bold'       =>  true
                    ));
                });
                $sheet->mergeCells('A1:'.$columnChar.'1');
                $sheet->setBorder('A2:'.$columnChar.count($data), 'thin');
                $sheet->cells('A1:'.$columnChar.count($data), function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });
                // 生成表
                $sheet->fromArray($data,  null, 'A0', true);
            });

        })->download('xls');
    }
}
