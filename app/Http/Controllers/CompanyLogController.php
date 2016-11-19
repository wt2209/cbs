<?php

namespace App\Http\Controllers;

use App\Model\Room;
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
        $companyLogs = CompanyLog::paginate(config('cbs.pageNumber'));
        return view('companyLog/index', ['companyLogs'=>$companyLogs]);
    }

    /**
     * 搜索
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getSearch(Request $request)
    {
        $companyName = trim(strip_tags(htmlspecialchars($request->company_name)));
        $roomName = $request->room_name;
        if (empty($companyName) && empty($roomName)) {
            $companyLogs = CompanyLog::paginate(config('cbs.pageNumber'));
        } elseif(empty($companyName)) {
            $rooms = Room::where('room_name', 'like', '%'.$roomName.'%')->lists('room_id')->toArray();
            $companyLogs = CompanyLog::whereIn('room_id', $rooms)->paginate(config('cbs.pageNumber'));
        } else {
            $companies = Company::where('company_name', 'like', '%'.$companyName.'%')->lists('company_id')->toArray();
            $companyLogs = CompanyLog::whereIn('company_id', $companies)->paginate(config('cbs.pageNumber'));
        }
        return view('companyLog/index', ['companyLogs'=>$companyLogs]);
    }

    /**
     * 生成房间变动日志
     * @param $companyId
     * @param $userId
     * @param array $oldRooms
     * @param array $newRooms
     */
    static public function log($companyId,$isNewCompany,  $userId, $oldRooms=[], $newRooms=[])
    {
        $results = [];
        foreach($oldRooms as $oldRoom) {
            $tmpArr = explode('_', $oldRoom);
            $results[$tmpArr[0]]['pre_rent_type'] = $tmpArr[1];
            $results[$tmpArr[0]]['pre_gender'] = $tmpArr[2];
        }
        foreach($newRooms as $newRoom) {
            $tmpArr = explode('_', $newRoom);
            $results[$tmpArr[0]]['new_rent_type'] = $tmpArr[1];
            $results[$tmpArr[0]]['new_gender'] = $tmpArr[2];
        }

        foreach ($results as $roomId => $result) {
            $companyLog = new CompanyLog();
            $companyLog->company_id = $companyId;
            $companyLog->user_id = $userId;
            $companyLog->room_id = $roomId;
            $companyLog->pre_rent_type = isset($result['pre_rent_type']) ? $result['pre_rent_type'] : '';
            $companyLog->pre_gender = isset($result['pre_gender']) ? $result['pre_gender'] : '';
            $companyLog->new_rent_type = isset($result['new_rent_type']) ? $result['new_rent_type'] : '';
            $companyLog->new_gender = isset($result['new_gender']) ? $result['new_gender'] : '';

            //跳过没有变动的项目
            if (isset($result['new_rent_type']) && isset($result['pre_rent_type'])) {
                if ($result['new_rent_type'] == $result['pre_rent_type']
                    && $result['new_gender'] == $result['pre_gender']){
                    continue;
                }
            }

            if($isNewCompany == 1){ //新公司入住
                $companyLog->room_change_type = 0;
            }elseif (!isset($result['new_rent_type'])
                && isset($result['pre_rent_type'])) { //只减少房间
                $companyLog->room_change_type = 2;
            } elseif (!isset($result['pre_rent_type'])
                && isset($result['new_rent_type'])) {//只增加房间
                $companyLog->room_change_type = 1;
            } else {
                //跳过餐厅和服务用房
                if (Room::where('room_id', $roomId)->value('room_type') != 1) {
                    continue;
                }
                if ($result['new_rent_type'] != $result['pre_rent_type']
                    && $result['new_gender'] != $result['pre_gender']) { //性别和人数变动
                    $companyLog->room_change_type = 5;
                } elseif ($result['new_gender'] == $result['pre_gender']) { //只是人数变动
                    $companyLog->room_change_type = 3;
                } else { // 只是性别变动
                    $companyLog->room_change_type = 4;
                }
            }

            $companyLog->save();
        }
    }

    /**
     * 填写变动房间水电底数
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getUtilityOfChangedRooms()
    {
        $companyLogs = CompanyLog::where('water_base', 0)
            ->where('electric_base', 0)
            ->whereIn('room_change_type', [1,2,3,5])
            ->get();
        return view('companyLog.utilityOfChangedRooms', ['companyLogs' => $companyLogs]);
    }

    /**
     * 存储变动房间水电底数
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postUtilityOfChangedRooms(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            if (is_array($value) && count($value) == 2) {
                //TODO 有可能会找不到，从而出错
                $companyLog = CompanyLog::find(intval($key));
                $companyLog->water_base = intval($value['water_base']);
                $companyLog->electric_base = intval($value['electric_base']);
                $companyLog->save();
            }
        }
        return response()->json(['message'=>'操作成功！','status'=>1]);
    }


    /**
     * 修改水电底数
     * @param $companyLogId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getEditBase($companyLogId)
    {
        $companyLog = CompanyLog::find($companyLogId);
        return view('companyLog.editBase', ['companyLog'=>$companyLog]);
    }

    /**
     * 存储水电底数
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postEditBase(Request $request)
    {
        $companyLog = CompanyLog::find($request->cl_id);
        $companyLog->water_base = $request->water_base;
        $companyLog->electric_base = $request->electric_base;
        $companyLog->save();
        return response()->json(['message'=>'操作成功！', 'status'=>1]);
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