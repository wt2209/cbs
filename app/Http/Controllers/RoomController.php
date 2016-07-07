<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model\Room;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    public function __construct()
    {
        $this->middleware('my.auth');
        $this->middleware('fieldFilter', ['only'=>['postStore']]);
    }


    public function getAllRentType()
    {
        $rentType = DB::table('rent_type')->get();
        return response()->json($rentType);
    }

    /**
     * 获取空房间
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllEmptyRoom()
    {
        $rooms = Room::where('company_id', 0)->get();
        $return = [];
        foreach ($rooms as $room) {
            switch ($room['room_type']) {
                case '1':
                    $return['living'][] = [
                        'room_id'=>$room->room_id,
                        'room_name'=>$room->room_name,
                        'rent_type_id'=>$room->rent_type_id,
                        'person_number'=>$room->rentType->person_number
                    ];
                    break;
                case '2':
                    $return['dining'][] = [
                        'room_id'=>$room->room_id,
                        'room_name'=>$room->room_name,
                        'rent_type_id'=>$room->rent_type_id,
                        'person_number'=>$room->rentType->person_number
                    ];
                    break;
                case '3':
                    $return['service'][] = [
                        'room_id'=>$room->room_id,
                        'room_name'=>$room->room_name,
                        'rent_type_id'=>$room->rent_type_id,
                        'person_number'=>$room->rentType->person_number
                    ];
                    break;
            }
        }
        return response()->json($return);
    }

    /*
     * 所有住房
     **/
    public function getLivingRoom()
    {
        $count = $this->countRoomNumber('living');
        return view('room.livingRoom', ['rooms' => Room::where('room_type',1)->paginate(config('cbs.pageNumber')), 'count'=>$count]);
    }

    /*
     * 所有服务用房
     **/
    public function getDiningRoom()
    {
        $count = $this->countRoomNumber('dining');
        return view('room.diningRoom', ['rooms' => Room::where('room_type',2)->paginate(config('cbs.pageNumber')), 'count'=>$count]);
    }

    /*
     * 所有餐厅
     **/
    public function getServiceRoom()
    {
        $count = $this->countRoomNumber('service');
        return view('room.serviceRoom', ['rooms' => Room::where('room_type',3)->paginate(config('cbs.pageNumber')), 'count'=>$count]);
    }


    /**
     * 搜索
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getSearch(Request $request)
    {
        $roomName = trim(strip_tags(htmlspecialchars($request->room_name)));
        $roomStatus = intval($request->room_status);
        $roomType = intval($request->room_type);

        $pageType =  $roomType === 1 ? "living" : ($roomType === 2 ? 'dining' : 'service');

        if (!empty($roomName)) {
            $whereArr[] = "room_name = '{$roomName}'";
        }
        if ($roomStatus === 1) {
            $whereArr[] = "company_id != 0";
        } elseif ($roomStatus === 2) { //空房间。company_id=0
            $whereArr[] = "company_id = 0";
        }
        $whereArr[] = "room_type = {$roomType}";
        $where = implode(' and ', $whereArr);


        $count = $this->countRoomNumber($pageType, $where);
        if ($where) {
            $rooms =  Room::whereRaw($where)->paginate(1);
        } else {
            $rooms =  Room::paginate(config('cbs.pageNumber'));
        }
        return view('room.'. $pageType .'Room', ['rooms' => $rooms, 'count'=>$count]);
    }

    /**
     * 添加房间
     * @return \Illuminate\View\View
     */
    public function getAdd()
    {
        return view('room.add');
    }

    public function getEdit($id)
    {
        $id = isset($id) ? (int)$id : 0;
        if (!$id)
            return redirect()->back();

        $room = Room::where('room_id', $id)->first();

        return view('room.edit', ['room'=>$room]);
    }

    /**
     * 根据room_id删除数据
     * @param Request $request
     */
    // TODO
    public function getRemove(Request $request)
    {
        //验证房间id
        $roomId = (int)$request->delete_id;
        if (!$roomId) {
            exit();
        }
        
        //若当前房间有人居住，则禁止删除
        if (Room::where('room_id', $roomId)
                ->where('company_id', '>', 0)
                ->count() > 0) {
            exit(json_encode(['message'=>'失败：此房间正在使用，不能删除！', 'status'=>0]));
        }

        if (Room::destroy($roomId)) {
            exit(json_encode(['message'=>'删除成功！', 'status'=>1]));
        } else {
            exit(json_encode(['message'=>'失败：删除数据时发生错误，请重试...', 'status'=>0]));
        }
    }

    /**
     * 新增数据
     * @param Request $request
     */
    public function postStore(Request $request)
    {
        //字段验证
        $validator = Validator::make($request->all(), [
            'room_id'=>'integer|min:1',
            'room_name' => 'required'
        ]);

        //验证不通过，返回第一个错误信息
        if ($validator->fails()) {
            exit(json_encode(['message'=>$validator->errors()->first(), 'status'=>0]));
        }

        //检测是否已经录入过此房间
        $count = Room::where('room_name', $request->room_name)
            ->count();
        //若添加的房间已存在，则返回错误
        if ($count > 0) {
            exit(json_encode(['message'=>'失败：此房间已经存在!', 'status'=>0]));
        }

        //新建模型
        $room = new Room();
        $room->room_name = $request->room_name;
        $room->room_type = $request->room_type;
        $room->room_remark = $request->room_remark;

        if ($room->save()) {
            exit(json_encode(['message'=>'操作成功！', 'status'=>1]));
        } else {
            exit(json_encode(['message'=>'失败：数据添加失败，请重试...', 'status'=>0]));
        }
    }

    public function postUpdate(Request $request)
    {
        $roomId = intval($request->room_id);
        $roomRemark = trim(htmlspecialchars(strip_tags($request->room_remark)));

        if (DB::table('room')->where('room_id', $roomId)->update(['room_remark'=>$roomRemark])) {
            return response()->json(['message'=>'操作成功！', 'status'=>1]);
        }
        return response()->json(['message'=>'失败：数据添加失败，请重试...', 'status'=>0]);
    }

    /**
     * 统计房间总数及空房间数
     * @param null $where 必须满足laravel中where参数的要求
     * @return array
     */
    private function countRoomNumber($type, $where = NULL)
    {
        switch ($type) {
            case 'living':
                $whereArr[] = 'room_type = 1'; //所有住房
                break;
            case 'dining':
                $whereArr[] = 'room_type = 2'; //所有住房
                break;
            case 'service':
                $whereArr[] = 'room_type = 3'; //所有住房
                break;
        }
        if ($where) {
            $whereArr[] = $where;
        }
        $whereStr = implode(' and ', $whereArr);

        $rooms = Room::whereRaw($whereStr)->get();
        $count['all'] = count($rooms);
        $count['empty'] = 0;
        foreach ($rooms as $room) {
            if ($room->company_id == 0) {
                $count['empty']++;
            }
        }
        return $count;
    }
}