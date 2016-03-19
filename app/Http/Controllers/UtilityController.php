<?php

namespace App\Http\Controllers;

use DB;
use App\Model\Company;
use App\Model\Room;
use App\Model\UtilityBase;
use Illuminate\Http\Request;
use App\Http\Controllers\CompanyLogController;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UtilityController extends Controller{
    public function __construct()
    {
        $this->middleware('fieldFilter', ['only'=>['postStore']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getIndex()
    {
        return view('utility.index');
    }


    /**
     * 录入水电底数
     */
    public function getAdd()
    {
        return view('utility.add');
    }

    /**
     * 水电表底数
     */
    public function getBase()
    {
        $bases = UtilityBase::all();
        $roomToId = $this->setRoomToId();
        $idToRoom = array_flip($roomToId);
        foreach ($bases as $k => $base) {
            $bases[$k]['room'] = $idToRoom[$base['room_id']];
        }
        return view('utility.base', ['bases'=>$bases]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function postStore(Request $request)
    {
        $result = $request->all();


        $year = intval($result['year']);
        $month = intval($result['month']);
        $recorder = addslashes(strip_tags($result['recorder']));
        $recordTime = strtotime(str_replace('.', '-', $result['record_time'])) ?
            str_replace('.', '-', $result['record_time']) : date('Y-m-d H:i:s');

        if (!($year && $month)) {
            return response()->json(['message'=>'错误：请输入正确的年份和月份！', 'status'=>0]);
        }

        $roomToId = $this->setRoomToId();
        $i = 1;
        $tmp = [];

        while (isset($result[(string)$i]['room'])) {
            $currentRoom = addslashes(strip_tags($result[(string)$i]['room']));
            if (isset($roomToId[$currentRoom])) {
                $tmp[] = [
                    'room_id'       =>intval($roomToId[$currentRoom]),
                    'electric_base'=>intval($result[(string)$i]['electric_base']),
                    'water_base'    =>intval($result[(string)$i]['water_base']),
                    'u_base_remark' =>addslashes(strip_tags($result[(string)$i]['u_base_remark'])),
                    'recorder'      =>$recorder,
                    'record_time'   =>$recordTime,
                    'year'          =>$year,
                    'month'         =>$month
                ];
            }
            $i++;
        }

        DB::beginTransaction();
        if (DB::table('utility_base')->insert($tmp)) {
            DB::commit();
            return response()->json(['message'=>'操作成功！', 'status'=>1]);
        }
        DB::rollBack();
        return response()->json(['message'=>'错误：数据添加失败，请重试...', 'status'=>0]);
    }

    private function setRoomToId()
    {
        $rooms = Room::all();
        if(!$rooms) {
            return false;
        }
        $roomToId = [];
        foreach ($rooms as $room) {
            $roomToId[$room['building'] . '-' . $room['room_number']] = $room['room_id'];
        }
        return $roomToId;
    }


    public function postCalculate(Request $request)
    {
        $year = $request->year ? intval($request->year) : 0;
        $month = $request->month ? intval($request->month) : 0;
        if (!$year || !$month || $month > 12 || $month < 1) {
            return response()->json(['message'=>"错误：请输入正确的年份和月份！", 'status'=>0]);
        }
        if ($month = 1) {
            $preYear = $year - 1;
            $preMonth = 12;
        } else {
            $preYear = $year;
            $preMonth = $month - 1;
        }

        //只计算非空房间的水电费
        $utilityBase = DB::table('utility_base')
                                ->leftJoin('room', 'room_id', '=', 'room.room_id')
                                ->where('room.company_id', '!=', 0)
                                ->where(function ($query){
                                    $query->where('year', 2015)
                                    ->orWhere('year', 2015);
                                })
                                ->get();

        return response()->json(['message'=>$utilityBase, 'status'=>0]);

      /*  $preMonthUtilityBase = DB::table('utility_base')
                                        ->join('room', 'room_id', '=', 'room.room_id')
                                        ->where([
                                            ['year', $preYear],
                                            ['month', $preMonth],
                                            ['room.company_id', '!=', 0]
                                        ])
                                        ->get();*/

        /*$notEmptyRooms = DB::table('room')->where('company_id', '!=', 0)->lists('room_id');

        //查找水电底数
        $currentMonthUtilityBase = DB::table('utility_base')
                            ->where([
                                ['year', $year],
                                ['month', $month]
                            ])
                            ->whereIn('room_id', $notEmptyRooms)
                            ->get();
        $preMonthUtilityBase = DB::table('utility_base')
                            ->where([
                                ['year', $preYear],
                                ['month', $preMonth]
                            ])
                            ->whereIn('room_id', $notEmptyRooms)
                            ->get();*/


    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}