<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model\Room;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    public function __construct()
    {
        $this->middleware('fieldFilter', ['only'=>['postStore']]);
    }

    /**
     * 获取空房间
     */
    public function getEmptyRoom()
    {
        $rooms = Room::where('company_id', 0)->get();
        $return = [];
        foreach ($rooms as $room) {
            $return[] = [
                'room_id'=>$room->room_id,
                'room_name'=>$room->building . '-' . $room->room_number,
            ];
        }
        exit(json_encode($return));
    }

    /*
     * TODO
     * 1.分页
     **/
    public function getIndex()
    {
        //TODO 分页中每一页的数量设置成一个配置项
        $count = $this->setRoomCount();
        return view('room.index', ['rooms' => Room::paginate(20), 'count'=>$count]);
    }

    public function getSearch(Request $request)
    {
        $roomName = trim(strip_tags(htmlspecialchars($request->room_name)));
        $roomType = intval($request->room_type);

        if (!empty($roomName)) {
            $tmpArr = explode('-', $roomName);
            $where = "building = $tmpArr[0] and room_number = $tmpArr[1]";
        } elseif ($roomType === 1) {
            $where = "company_id != 0";
        } elseif ($roomType === 2) { //空房间。company_id=0
            $where = "company_id = 0";
        } else {
            $where = NULL;
        }
        //TODO 分页中每一页的数量设置成一个配置项
        $count = $this->setRoomCount($where);
        if ($where) {
            $rooms =  Room::whereRaw($where)->paginate(1);
        } else {
            $rooms =  Room::paginate(1);
        }
        return view('room.index', ['rooms' => $rooms, 'count'=>$count]);
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
            exit(json_encode(['message'=>'失败：此房间有人居住，不能删除！', 'status'=>0]));
        }

        if (Room::destroy($roomId)) {
            exit(json_encode(['message'=>'删除成功！', 'status'=>1]));
        } else {
            exit(json_encode(['message'=>'失败：删除数据时发生错误，请重试...', 'status'=>0]));
        }
    }

    /**
     * 存储数据，包括新增数据和修改数据
     * @param Request $request
     */
    public function postStore(Request $request)
    {
        //字段验证
        $validator = Validator::make($request->all(), [
            'room_id'=>'integer|min:1',
            'building' => 'required',
            'room_number' => 'required|integer|max:65535|min:1',
        ]);

        //验证不通过，返回第一个错误信息
        if ($validator->fails()) {
            exit(json_encode(['message'=>$validator->errors()->first(), 'status'=>0]));
        }

        //检测是否已经录入过此房间
        $count = Room::where('building', $request->building)
            ->where('room_number', $request->room_number)
            ->count();
        //若添加的房间已存在，则返回错误
        if ($count > 0) {
            exit(json_encode(['message'=>'失败：此房间已经存在!', 'status'=>0]));
        }

        //新建实例
        if ($request->room_id) { // 存在room_id，修改数据。由于是魔术方法，因此不能使用isset($request->room_id)
            $room = Room::findOrFail($request->room_id);//会自动进行错误处理
        } else { //新增数据
            //新建模型
            $room = new Room();
        }
        $room->building = $request->building;
        $room->room_number = $request->room_number;
        $room->room_remark = $request->room_remark;

        if ($room->save()) {
            exit(json_encode(['message'=>'操作成功！', 'status'=>1]));
        } else {
            exit(json_encode(['message'=>'失败：数据添加失败，请重试...', 'status'=>0]));
        }
    }

    /**
     * 统计房间总数及空房间数
     * @param null $where 必须满足laravel中where参数的要求
     * @return array
     */
    private function setRoomCount($where = NULL)
    {
        if ($where) {
            $rooms = Room::whereRaw($where)->get();
        } else {
            $rooms = Room::get();
        }
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