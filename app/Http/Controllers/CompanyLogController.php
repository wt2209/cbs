<?php

namespace App\Http\Controllers;

use DB;
use App\Model\CompanyLog;
use App\Model\Company;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CompanyLogController extends Controller
{

    public function __construct()
    {
        $this->middleware('my.auth');
    }
    /**
     * 首页
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        $companyLogs = DB::table('company_log')->paginate(config('cbs.pageNumber'));
        return view('companyLog/index', ['companyLogs'=>$companyLogs]);
    }
    public function getSearch(Request $request)
    {
        $companyName = trim(strip_tags(htmlspecialchars($request->company_name)));
        $companyLogs = DB::table('company_log')->where('company_name', 'like', '%' . $companyName . '%')
            ->paginate(1);
        return view('companyLog/index', ['companyLogs'=>$companyLogs]);
    }

    /**
     * * 记录房间操作的历史日志
     * @param $type 操作类型，1|入住 2|调整房间 3|退房 4|删除
     * @param $companyId 房间id
     * @param $oldRooms 老房间
     * @param null $newRooms 新房间
     * @return bool
     */
    static public function log($companyId, $oldRooms=[], $newRooms=[])
    {
        $newRoomsFlip = array_flip($newRooms);
        $oldRoomsFlip = array_flip($oldRooms);

        foreach ($oldRooms as $oldRoom) {
            if (isset($newRoomsFlip[$oldRoom])) {
                unset($newRoomsFlip[$oldRoom]);
            }
        }
        foreach ($newRooms as $newRoom) {
            if (isset($oldRoomsFlip[$newRoom])) {
                unset($oldRoomsFlip[$newRoom]);
            }
        }


        if (empty($newRoomsFlip)) { //只减少房间
            foreach ($oldRoomsFlip as $key) {
                $tmpArr = explode('_', $key);
                CompanyLog::insert([
                    'room_change_type'=>2,
                    'company_id'=>$companyId,
                    'room_id'=>$tmpArr[0],
                    'pre_rent_type'=>$tmpArr[1],
                    'pre_gender'=>$tmpArr[2]
                ]);
            }
        } elseif (empty($oldRoomsFlip)) { //只增加房间
            foreach ($newRoomsFlip as $key) {
                $tmpArr = explode('_', $key);
                CompanyLog::insert([
                    'room_change_type'=>1,
                    'company_id'=>$companyId,
                    'room_id'=>$tmpArr[0],
                    'new_rent_type'=>$tmpArr[1],
                    'new_gender'=>$tmpArr[2]
                ]);
            }
        } else { //有可能人数变动，或性别变动，或两者均变动   也有可能只是餐厅和服务用房变动

        }
/*
        echo '<pre>newRoomsFlip:<br>';
        print_r($newRoomsFlip);
        echo '<br>oldRoomsFlip:<br>';
        print_r($oldRoomsFlip);
        dd('end');*/






        $change = array_diff(array_unique(array_merge($oldRooms, $newRooms)), $newRooms);
        dd($change);
    }

    /**
     * 删除操作记录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDelete(Request $request)
    {
        $deleteId = intval($request->delete_id);
        if (!$deleteId) {
            return response()->json(['message'=>"错误：请正确操作！", 'status'=>0]);
        }
        if (CompanyLog::destroy($deleteId)) {
            return response()->json(['message'=>"操作成功！", 'status'=>1]);
        }
        return response()->json(['message'=>"错误：请重试！", 'status'=>0]);
    }
}