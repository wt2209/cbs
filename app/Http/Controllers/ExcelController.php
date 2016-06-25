<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {
        $date = date('Ymd');
        Excel::create('test2016.6.25', function($excel){
            $excel->sheet('房费', function($sheet) {
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
                $titleRow = ['2016.5月份房费',''];
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
                for ($i = 2; $i < $columnNumber - 1; $i++) {
                    $sheet->setWidth($chars[$i], 8.5);
                }
                $sheet->setWidth($chars[$columnNumber - 1], 20);
                $sheet->setBorder('A2:'.$chars[$columnNumber - 1].($serialNumber + 2), 'thin');
                $sheet->cells('A1:'.$chars[$columnNumber - 1] .($serialNumber + 2), function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                });
                // 生成表
                $sheet->fromArray($data,  null, 'A0', true);
            })->sheet('房费11')->download('xls');

        });
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
