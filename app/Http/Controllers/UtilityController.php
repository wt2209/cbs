<?php

namespace App\Http\Controllers;

use DB;
use App\Model\Company;
use App\Model\Utility;
use App\Model\Room;
use App\Model\UtilityBase;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UtilityController extends Controller{
    public function __construct()
    {
        $this->middleware('fieldFilter', ['only'=>['postStore']]);
    }

    /**
     * 水电费首页
     * @return Response
     */
    public function getIndex()
    {
        $utilities = DB::table('utility')
            ->join('room', 'utility.room_id', '=', 'room.room_id')
            ->join('company', 'company.company_id', '=', 'room.company_id')
            ->get();
        return view('utility.index', ['utilities'=>$utilities]);
    }

    /**
     * 录入底数
     * @return \Illuminate\View\View
     */
    public function getAdd()
    {
        return view('utility.add');
    }

    /**
     * 水电表底数首页
     * @return \Illuminate\View\View
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
        $insert = [];
        while (isset($result[(string)$i]['room'])) {
            $currentRoom = addslashes(strip_tags($result[(string)$i]['room']));
            if (isset($roomToId[$currentRoom])) {
                $insert[] = [
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
        if (DB::table('utility_base')->insert($insert)) {
            DB::commit();
            return response()->json(['message'=>'操作成功！', 'status'=>1]);
        }
        DB::rollBack();
        return response()->json(['message'=>'错误：数据添加失败，请重试...', 'status'=>0]);
    }

    /**
     * 计算水电费
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postCalculate(Request $request)
    {
        $year = $request->year ? intval($request->year) : 0;
        $month = $request->month ? intval($request->month) : 0;
        if (!$year || !$month || $month > 12 || $month < 1) {
            return response()->json(['message'=>"错误：请输入正确的年份和月份！", 'status'=>0]);
        }

        $insert = $this->setInsertData($year, $month);

        DB::beginTransaction();
        //如果有以前录入的重复的数据，则删除
        DB::table('utility')
                ->where('year', $year)
                ->where('month', $month)
                ->whereIn('room_id', array_keys($insert))
                ->delete();

        if (DB::table('utility')->insert($insert)) {
            DB::commit();
            return response()->json(['message'=>'操作成功！', 'status'=>1]);
        }
        DB::rollBack();
        return response()->json(['message'=>'错误：数据添加失败，请重试...', 'status'=>0]);
    }

    /**
     * 编辑水电费
     * @param  int  $id
     * @return Response
     */
    public function getEdit($UtilityId)
    {
        $utility = Utility::join('room', 'room.room_id', '=', 'utility.room_id')
                        ->find($UtilityId);
        return view('utility.edit', ['utility'=>$utility]);
    }

    /**
     * 编辑水电底数
     * @param $UtilityBaseId
     * @return \Illuminate\View\View
     */
    public function getEditBase($UtilityBaseId)
    {
        $utilityBase = UtilityBase::join('room', 'room.room_id', '=', 'utility_base.room_id')
            ->find($UtilityBaseId);
        return view('utility.editBase', ['utilityBase'=>$utilityBase]);
    }

    /**
     *  更新水电费
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function postUpdate(Request $request)
    {
        if (is_numeric($request->utility_id)
                && is_numeric($request->water_money)
                && is_numeric($request->electric_money)) {
            Utility::where('utility_id', intval($request->utility_id))
                ->update([
                    'water_money'=>$request->water_money,
                    'electric_money'=>$request->electric_money,
                    'utility_remark'=>addslashes(strip_tags(trim($request->utility_remark)))
                ]);
            return response()->json(['message'=>'操作成功！', 'status'=>1]);
        }
        return response()->json(['message'=>'错误：请输入正确的信息！', 'status'=>0]);
    }

    /**
     * 更新水电底数
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postUpdateBase(Request $request)
    {
        if (is_numeric($request->u_base_id)) {
            UtilityBase::where('u_base_id', intval($request->u_base_id))
                ->update([
                    'water_base'=>intval($request->water_base),
                    'electric_base'=>intval($request->electric_base),
                    'year'=>intval($request->year),
                    'month'=>intval($request->month),
                    'recorder'=>addslashes(strip_tags(trim($request->recorder))),
                    'u_base_remark'=>addslashes(strip_tags(trim($request->u_base_remark)))
                ]);
            return response()->json(['message'=>'操作成功！', 'status'=>1]);
        }
        return response()->json(['message'=>'错误：请输入正确的信息！', 'status'=>0]);
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

    /**
     * 建立房间名与房间id的映射
     * @return array|bool
     */
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

    /**
     * 组合出需要插入到数据库中的数组
     * @param $year
     * @param $month
     * @return array
     */
    private function setInsertData($year, $month)
    {
        $insert = [];
        $items = $this->setUtilityItem($year, $month);
        //TODO 水电费单价以及精度应该设置配置项
        foreach ($items as $roomId => $item) {
            //必须两个月的水电的、底数都存在才能计算水电费
            if (!isset($item['current']) || !isset($item['pre'])) {
                continue;
            }
            $allRoomIds[] = $roomId;
            $insert[$roomId] = [
                'room_id'=>$roomId,
                'company_id'=>$item['current']['company_id'],
                'water_money'
                =>round(3.35*($item['current']['water_base'] - $item['pre']['water_base']), 2),
                'electric_money'
                =>round(0.55*($item['current']['electric_base'] - $item['pre']['electric_base']), 2),
                'year'=>$year,
                'month'=>$month
            ];
        }
        return $insert;
    }

    /**
     * 查找相关底数
     * @param $year
     * @param $month
     * @return array
     */
    private function setUtilityItem($year, $month)
    {
        if ($month == 1) {
            $preYear = $year - 1;
            $preMonth = 12;
        } else {
            $preYear = $year;
            $preMonth = $month - 1;
        }
        //相关的两个月的水电底数
        $utilityBases = DB::table('utility_base')
            ->leftJoin('room', 'utility_base.room_id', '=', 'room.room_id')
            ->where('room.company_id', '!=', 0)
            ->whereIn('year', [$year, $preYear])
            ->whereIn('month', [$month, $preMonth])
            ->get();

        $items = [];
        foreach ($utilityBases as $utilityBase) {
            if ($utilityBase->year == $year
                && $utilityBase->month == $month) {
                $items[$utilityBase->room_id]['current'] = [
                    'company_id'    => $utilityBase->company_id,
                    'water_base'    => $utilityBase->water_base,
                    'electric_base' =>$utilityBase->electric_base
                ];
            } else {
                $items[$utilityBase->room_id]['pre'] = [
                    'company_id'    => $utilityBase->company_id,
                    'water_base'    => $utilityBase->water_base,
                    'electric_base' =>$utilityBase->electric_base
                ];
            }
        }
        return $items;
    }
}