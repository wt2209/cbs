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
        //TODO 分页中每一页的数量设置成一个配置项
        $utilities = DB::table('utility')
            ->join('room', 'utility.room_id', '=', 'room.room_id')
            ->join('company', 'company.company_id', '=', 'utility.company_id')
            ->paginate(20);

        $count = $this->setUtilityCount();
        return view('utility.index', ['utilities'=>$utilities, 'count'=>$count]);
    }

    /**
     * 搜索水电费
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function getSearch(Request$request)
    {
        $roomName = trim(strip_tags(htmlspecialchars($request->room_name)));
        $companyName = trim(strip_tags(htmlspecialchars($request->company_name)));
        $yearMonth = trim(strip_tags(htmlspecialchars($request->year_month)));
        $chargeType = intval($request->charge_type);

        $whereArr = [];
        if (empty($roomName)) { //房间号空
            if (!empty($companyName)) { //公司名不空：不用处理房间号，只用处理公司名
                $whereArr[] = "cbs_company.company_name like '%{$companyName}%'";
            }
        } else { //房间号不空，不用处理公司名
            $tmpRoom = explode('-', $roomName);
            if (count($tmpRoom) === 2) {//房间格式不对，给一个不能查出结果的条件
                $whereArr[] = 'cbs_room.building = ' . $tmpRoom[0] . ' and cbs_room.room_number = ' . $tmpRoom[1];
            }
        }
        if (!empty($yearMonth)) {
            $tmp = explode('-', $yearMonth);
            $year = isset($tmp[0]) ? intval($tmp[0]) : 0;
            $month = isset($tmp[1]) ? intval($tmp[1]) : 0;
            $whereArr[] = "year = {$year} and month = {$month}";
        }

        if ($chargeType === 1) {//已缴费
            $whereArr[] = "is_charged = 1";
        }
        if ($chargeType === 2) {//未交费
            $whereArr[] = "is_charged = 0";
        }

        $where = implode(' and ', $whereArr);
        if (!$where) { //条件为空，显示所有结果
            $where = 'utility_id != 0';
        }
        //TODO 分页
        $utilities = DB::table('utility')
            ->join('room', 'utility.room_id', '=', 'room.room_id')
            ->join('company', 'company.company_id', '=', 'utility.company_id')
            ->whereRaw($where)
            ->paginate(1);

        $count = $this->setUtilityCount($where);
        return view('utility.index', ['utilities'=>$utilities, 'count'=>$count]);
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
        //TODO 分页中每一页的数量设置成一个配置项
        $count = $this->setBaseCount();
        $bases = DB::table('utility_base')
            ->join('room', 'utility_base.room_id', '=', 'room.room_id')
            ->paginate(3);
        return view('utility.base', ['bases'=>$bases, 'count'=>$count]);
    }

    public function getBaseSearch(Request $request)
    {
        $roomName = trim(strip_tags(htmlspecialchars($request->room_name)));
        $yearMonth = trim(strip_tags(htmlspecialchars($request->year_month)));

        $whereArr = [];
        if (!empty($roomName)) { //房间号不空，不用处理公司名
            $tmpRoom = explode('-', $roomName);
            if (count($tmpRoom) === 2) {//房间格式不对，给一个不能查出结果的条件
                $whereArr[] = 'cbs_room.building = ' . $tmpRoom[0] . ' and cbs_room.room_number = ' . $tmpRoom[1];
            }
        }
        if (!empty($yearMonth)) {
            $tmp = explode('-', $yearMonth);
            $year = isset($tmp[0]) ? intval($tmp[0]) : 0;
            $month = isset($tmp[1]) ? intval($tmp[1]) : 0;
            $whereArr[] = "year = {$year} and month = {$month}";
        }

        $where = implode(' and ', $whereArr);
        if (!$where) { //条件为空，显示所有结果
            $where = 'u_base_id != 0';
        }
        //TODO 分页中每一页的数量设置成一个配置项
        $count = $this->setBaseCount();
        $bases = DB::table('utility_base')
            ->join('room', 'utility_base.room_id', '=', 'room.room_id')
            ->whereRaw($where)
            ->paginate(3);
        return view('utility.base', ['bases'=>$bases, 'count'=>$count]);
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
        //如果有以前计算的重复的数据，则删除
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
     * @param  int  $UtilityId
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
     * 单个房间缴费
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChargeSingleRoom(Request $request)
    {
        $utilityId = intval($request->charge_id);
        if (!$utilityId) {
            return response()->json(['message'=>"错误：请正确操作！", 'status'=>0]);
        }
        if (UtilityController::chargeStore([$utilityId])) {
            return response()->json(['message'=>"操作成功！", 'status'=>1]);
        }
        return response()->json(['message'=>"错误：请重试！", 'status'=>0]);
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDelete(Request $request)
    {
        $utilityId = intval($request->delete_id);
        if (!$utilityId) {
            return response()->json(['message'=>"错误：请正确操作！", 'status'=>0]);
        }
        if (Utility::destroy($utilityId)) {
            return response()->json(['message'=>"操作成功！", 'status'=>1]);
        }
        return response()->json(['message'=>"错误：请重试！", 'status'=>0]);
    }

    /**
     * 删除水电底数
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBaseDelete(Request $request)
    {
        $utilityBaseId = intval($request->delete_id);
        if (!$utilityBaseId) {
            return response()->json(['message'=>"错误：请正确操作！", 'status'=>0]);
        }
        if (UtilityBase::destroy($utilityBaseId)) {
            return response()->json(['message'=>"操作成功！", 'status'=>1]);
        }
        return response()->json(['message'=>"错误：请重试！", 'status'=>0]);
    }

    /**
     * 缴费存储
     * @param $utilityIdArray
     * @param null $dateTime
     * @return bool
     */
    public static function chargeStore($utilityIdArray, $dateTime = NULL)
    {
        $dateTime = $dateTime ? date("Y-m-d H:i:s", strtotime($dateTime)) : date("Y-m-d H:i:s");
        return DB::table('utility')
            ->whereIn('utility_id', $utilityIdArray)
            ->update([
                'is_charged'=>1,
                'charge_time'=>$dateTime
            ]);
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
        //不计算空房间的水电费
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

    /**
     * 统计水、电各项数据
     * @param null $where 必须满足laravel要求的where方法的参数
     * @return array
     */
    private function setUtilityCount($where = NULL)
    {
        if ($where == NULL) {
            $utilities = DB::table('utility')
                ->join('room', 'utility.room_id', '=', 'room.room_id')
                ->join('company', 'company.company_id', '=', 'room.company_id')
                ->get();
        } else {
            $utilities = DB::table('utility')
                ->join('room', 'utility.room_id', '=', 'room.room_id')
                ->join('company', 'company.company_id', '=', 'room.company_id')
                ->whereRaw($where)
                ->get();
        }

        $count = [
            'total_number'=>count($utilities),
            'is_charged'=>[
                'water_money'=>0,
                'electric_money'=>0
            ],
            'no_charged'=>[
                'water_money'=>0,
                'electric_money'=>0
            ]
        ];
        foreach ($utilities as $utility) {
            if ($utility->is_charged == 1) {
                $count['is_charged']['water_money'] += $utility->water_money;
                $count['is_charged']['electric_money'] += $utility->electric_money;
            } elseif ($utility->is_charged == 0) {
                $count['no_charged']['water_money'] += $utility->water_money;
                $count['no_charged']['electric_money'] += $utility->electric_money;
            }
        }
        return $count;
    }

    /**
     * 统计水电底数总数
     * @param null $where 必须满足laravel要求的where方法的参数
     * @return int
     */
    private function setBaseCount($where = NULL)
    {
        if ($where) {
            return DB::table('utility_base')
                ->join('room', 'utility_base.room_id', '=', 'room.room_id')
                ->whereRaw($where)
                ->count();
        } else {
            return DB::table('utility_base')
                ->join('room', 'utility_base.room_id', '=', 'room.room_id')
                ->count();
        }
    }
}