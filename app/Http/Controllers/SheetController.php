<?php

namespace App\Http\Controllers;

use PHPExcel_Worksheet;
use App\Model\Room;
use Illuminate\Http\Request;
use DB;
use Storage;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class SheetController extends Controller
{
    public function __construct()
    {
        $this->middleware('my.auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {

        $files = Storage::files('monthlySheet');
        foreach ($files as $k => $file) {
            $files[$k] = substr($file, strpos($file, '/')+1);
        }
        return view('sheet.index', ['files'=>$files]);
        // dd($files);
    }

    public function getCreate()
    {
        $currentDate = date('Ymd');
        $date['year'] = intval(substr($currentDate, 0, 4));
        $date['month'] = intval(substr($currentDate, 4, 2));
        $date['day'] = intval(substr($currentDate, 6, 2));

        //上半月生成上个月的报表，下半月生成这个月的报表
        if ($date['day'] < 15) { // 生成上月表单
            if ($date['month'] === 1) {
                $date['month'] = 12;
                $date['year'] = $date['year'] - 1;
            } else {
                $date['month'] = $date['month'] - 1;
            }
        }

        //文件名
        $fileName = $date['year'].'.'.$date['month'].'月报表 - '.date('Ymd');

        Excel::create($fileName, function($excel) use($date){
            $excel->sheet('房费', function($sheet) use($date){
                $companies = DB::table('company')->where('is_quit', 0)->get();
                $rooms = DB::table('room')->where('room_type', 1)->get();
                $rentTypes = DB::table('rent_type')->get();
                $chars = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];

                foreach ($rooms as $room) {
                    if (isset($count[$room->company_id][$room->rent_type_id])) {
                        $count[$room->company_id][$room->rent_type_id]++ ;
                    } else {
                        $count[$room->company_id][$room->rent_type_id] = 1 ;
                    }
                }
                //标题行
                $titleRow = [$date['year'].'.'.$date['month'].'月份房费',''];
                //菜单第一行
                $menuRow1 = ['序号','单位'];
                //菜单第二行
                $menuRow2 = ['',''];

                foreach ($rentTypes as $rentType) {
                    $titleRow[] = '';
                    $titleRow[] = '';
                    $titleRow[] = '';
                    $menuRow1[] = $rentType->person_number . '人间';
                    $menuRow1[] = '';
                    $menuRow1[] = '';
                    $menuRow2[] = '数量';
                    $menuRow2[] = '费用/间/月';
                    $menuRow2[] = '合计';
                }
                $titleRow[] = '';
                $titleRow[] = '';
                $menuRow1[] = '总计';
                $menuRow1[] = '备注';
                $menuRow2[] = '';
                $menuRow2[] = '';

                //总列数
                $columnNumber = count($titleRow);
                /* $data样式：
                    $data = [
                        ['2016.5月份房费', '','','','','','','','','','','',''],
                        ['序号','单位','6人间','','','8人间','','','12人间','','','总计','备注'],
                        ['','','数量','费用/间/月','合计','数量','费用/间/月','合计','数量','费用/间/月','合计','','']
                    ];*/

                $data = [
                    $titleRow,
                    $menuRow1,
                    $menuRow2
                ];
                // 序号
                $serialNumber = 1;
                foreach ($companies as $company) {
                    $tmpArr = [$serialNumber++, $company->company_name];
                    $companyTotalMoney = 0;
                    foreach ($rentTypes as $rentType) {
                        $number = isset($count[$company->company_id][$rentType->rent_type_id]) ?
                            $count[$company->company_id][$rentType->rent_type_id] :
                            0;
                        $money = $rentType->rent_money;
                        $totalMoney = $number * $money;
                        $companyTotalMoney += $totalMoney;
                        $tmpArr[] = $number;
                        $tmpArr[] = $money;
                        $tmpArr[] = $totalMoney;
                    }
                    $tmpArr[] = $companyTotalMoney;
                    $tmpArr[] = $company->company_remark;
                    //2, 588, 2111, 1, 888, 1111, 3, 1088, 3333, 'heji', $company->company_remark];
                    $data[] = $tmpArr;
                }
                $sheet->setRowsToRepeatAtTop(['1','2','3','4']);
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
                $sheet->setMergeColumn(array(
                    'columns' => array('A','B',$chars[3*count($rentTypes)+2],$chars[3*count($rentTypes)+3]),
                    'rows' => array(
                        array(2,3)
                    )
                ));
                $sheet->mergeCells('A1:'.$chars[$columnNumber - 1].'1');
                for ($i = 0; $i < count($rentTypes); $i++) {
                    $sheet->mergeCells($chars[$i*3+2].'2:'.$chars[$i*3+4].'2');
                }
                $sheet->setWidth('A', 5);
                $sheet->setWidth('B', 30);
                $sheet->setHeight(1, 25);
                for ($i = 2; $i < $columnNumber - 1; $i++) {
                    $sheet->setWidth($chars[$i], 8.5);
                }
                $sheet->setWidth($chars[$columnNumber - 1], 20);
                $sheet->setBorder('A2:'.$chars[$columnNumber - 1].($serialNumber + 2), 'thin');
                $sheet->cells('A1:'.$chars[$columnNumber - 1] .($serialNumber + 2), function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');

                });
                //设置表头自动换行
                $sheet->getStyle('A1:M3')->getAlignment()->setWrapText(true);

                // 生成表
                 $sheet->fromArray($data, null, 'A1', true, false);
            })->sheet('明细',function ($sheet) use($date){

                //TODO 只查询居住房间
                $rooms = Room::where('company_id', '!=', 0)
                    ->where('room_type', 1)
                    ->get();
                //$rooms = Room::where('room_id', 4)->get();

                if ($date['month'] === 1) {
                    $preDate = [
                        'year'=>$date['year'] - 1,
                        'month'=>12
                    ];
                } else {
                    $preDate = [
                        'year'=>$date['year'],
                        'month'=>$date['month'] - 1
                    ];
                }
                $currentBases = DB::table('utility_base')
                    ->where('year', $date['year'])
                    ->where('month', $date['month'])
                    ->get();
                $preBases = DB::table('utility_base')
                    ->where('year', $preDate['year'])
                    ->where('month', $preDate['month'])
                    ->get();
                $currentBaseArr = $preBaseArr = [];
                foreach ($currentBases as $currentBase) {
                    $currentBaseArr[$currentBase->room_id]['water_base'] = $currentBase->water_base;
                    $currentBaseArr[$currentBase->room_id]['electric_base'] = $currentBase->electric_base;
                }
                foreach ($preBases as $preBase) {
                    $preBaseArr[$preBase->room_id]['water_base'] = $preBase->water_base;
                    $preBaseArr[$preBase->room_id]['electric_base'] = $preBase->electric_base;
                }

                //设置表样式
                $sheet->setPageMargin(array(
                    0.4, 0.4, 0.4, 0.4
                ));
                $sheet->setStyle(array(
                    'font' => array(
                        'name' => '宋体',
                        'size' => 11,
                    )
                ));
                $sheet->mergeCells('A1:O1');
                $sheet->mergeCells('A2:B2');
                $sheet->mergeCells('M2:O2');
                $sheet->mergeCells('C3:M3');
                $sheet->mergeCells('C4:K4');
                $sheet->mergeCells('C5:F5');
                $sheet->mergeCells('G5:J5');

                $sheet->setMergeColumn(array(
                    'columns' => array('K'),
                    'rows' => array(
                        array(5, 6)
                    )
                ));
                $sheet->setMergeColumn(array(
                    'columns' => array('L', 'M'),
                    'rows' => array(
                        array(4, 6)
                    )
                ));
                $sheet->setMergeColumn(array(
                    'columns' => array('A', 'B', 'N', 'O'),
                    'rows' => array(
                        array(3, 6)
                    )
                ));

                $data = [
                    [$date['year'].'年'.$date['month'].'月份承包商公寓水电费明细表', '', '', '', '', '', '', '', '', '', '', '', '', '', ''],
                    ['单位：服务中心', '', '', '', '', '', '', '', '', '', '', '', '日期：'.date('Y.m.d'), '', ''],
                    ['房间号', '单位', '水、电费用', '', '', '', '', '', '', '', '', '', '', '服务费合计（元）', '备注'],
                    ['', '', '住房水电费', '', '', '', '', '', '', '', '', '食堂水电费合计（元）', '水电费合计（元）', '', ''],
                    ['', '', '水费（'. config('cbs.waterMoney') .'元/吨）', '', '', '', '电费（'.config('cbs.electricMoney').'元/度）', '', '', '', '合计（元）', '', '', '', ''],
                    ['', '', '上期数', '本期数', '实用数', '费用', '上期数', '本期数', '实用数', '费用', '', '', '', '', '']
                ];


                // 生成表

                $sheet->fromArray($data, null, 'A1', true, false);

                $currentRow = 6;
                $result = [];
                foreach ($rooms as $room) {
                    $preWaterBase = isset($preBaseArr[$room->room_id]['water_base'])
                        ? $preBaseArr[$room->room_id]['water_base']
                        : 0;
                    $currentWaterBase = isset($currentBaseArr[$room->room_id]['water_base'])
                        ? $currentBaseArr[$room->room_id]['water_base']
                        : 0;
                    $preElectricBase = isset($preBaseArr[$room->room_id]['electric_base'])
                        ? $preBaseArr[$room->room_id]['electric_base']
                        : 0;
                    $currentElectricBase = isset($currentBaseArr[$room->room_id]['electric_base'])
                        ? $currentBaseArr[$room->room_id]['electric_base']
                        : 0;
                    $waterMoney = round(config('cbs.waterMoney')*($currentWaterBase - $preWaterBase), config('cbs.precision'));
                    $electricMoney = round(config('cbs.electricMoney')*($currentElectricBase - $preElectricBase), config('cbs.precision'));
                    //食堂水电费
                    $diningMoney = 0;
                    //初始化
                    $tmp = [];
                    $tmp[] = $room->room_name;
                    $tmp[] = $room->company->company_name;
                    $tmp[] = $preWaterBase;
                    $tmp[] = $currentWaterBase;
                    $tmp[] = $currentWaterBase - $preWaterBase;//index:4
                    $tmp[] = $waterMoney; // index:5
                    $tmp[] = $preElectricBase;
                    $tmp[] = $currentElectricBase;
                    $tmp[] = $currentElectricBase - $preElectricBase;//index:8
                    $tmp[] = $electricMoney; //index:9
                    $tmp[] = $waterMoney + $electricMoney; //index : 10
                    // TODO 食堂水电费
                    $tmp[] = '';;//index : 11
                    //TODO 水电费合计
                    $tmp[] = ''; // index 12
                    //TODO 服务费合计
                    $tmp[] = '';
                    $tmp[] = $room->room_remark;
                    $result[$room->company_id][] = $tmp;
                }
                // 行数
                $rowNumber = 6;
                foreach ($result as $r) {
                    $insertData = [];
                    $companyTotal = [''];
                    $total = [];
                    foreach ($r as $i) {
                        $insertData[] = $i;
                        $total['companyName'] = $i[1].' 汇总';
                        $total['waterBaseTotal'] = isset($total['waterBaseTotal']) ? $total['waterBaseTotal'] + $i[4] : $i[4];
                        $total['waterMoneyTotal'] = isset($total['waterMoneyTotal']) ? $total['waterMoneyTotal'] + $i[5] : $i[5];
                        $total['electricBaseTotal'] = isset($total['electricBaseTotal']) ? $total['electricBaseTotal'] + $i[8] : $i[8];
                        $total['electricMoneyTotal'] = isset($total['electricMoneyTotal']) ? $total['electricMoneyTotal'] + $i[9] : $i[9];
                        $currentRow++;
                        $rowNumber++;
                    }
                    $companyTotal[] = $total['companyName'];
                    $companyTotal[] = '';
                    $companyTotal[] = '';
                    $companyTotal[] = $total['waterBaseTotal'];
                    $companyTotal[] = $total['waterMoneyTotal'];
                    $companyTotal[] = '';
                    $companyTotal[] = '';
                    $companyTotal[] = $total['electricBaseTotal'];
                    $companyTotal[] = $total['electricMoneyTotal'];

                    $currentRow++;
                    $rowNumber++;
                    $insertData[] = $companyTotal;
                    $sheet->rows($insertData);
                    // 插入分页符
                    $sheet->setBreak('A'.$currentRow, PHPExcel_Worksheet::BREAK_ROW);
                }

                $sheet->setBorder('A3:O'.$rowNumber, 'thin');
                $sheet->cells('A1:O'.$rowNumber, function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });
                //关闭自适应列宽
                $sheet->setAutoSize(false);

                $sheet->setWidth(array(
                    'A'=>6,
                    'B'=>22,
                    'C'=>7,
                    'D'=>7,
                    'E'=>7,
                    'F'=>7,
                    'G'=>7,
                    'H'=>7,
                    'I'=>7,
                    'J'=>7,
                    'K'=>7,
                    'L'=>7,
                    'M'=>7,
                    'N'=>7,
                    'O'=>20
                ));
                $sheet->cell('A1', function($cell) {
                    $cell->setFont(array(
                        'size'       => '16',
                        'bold'       =>  true
                    ));
                });
                $sheet->setHeight(1, 25);
                $sheet->setFreeze('A1');
                $sheet->setrowsToRepeatAtTop([1,6]);

                //设置表头自动换行
                $sheet->getStyle('A1:O6')->getAlignment()->setWrapText(true);

            });
        })->store('xls', storage_path('app/monthlySheet'));
        return response()->json(['message'=>'操作成功！', 'status'=>1]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDownload($fileName)
    {
        if (Storage::has('/monthlySheet/'.$fileName)) {
            return response()->download(storage_path('app').'/monthlySheet/'.$fileName, $fileName);
        }
    }

}
