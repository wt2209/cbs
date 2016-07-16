<?php

namespace App\Http\Controllers;

use DB;
use App\Model\Company;
use App\Model\Room;
use Illuminate\Http\Request;
use App\Http\Controllers\CompanyLogController;
use Route;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\ExcelController;

class CompanyController extends Controller
{
    /**
     * 当前操作的公司id。用于记录日志
     * @var
     */
    private $companyId;

    /**
     * 当前操作的类型。用于记录日志
     * 1|入住 2|调整房间 3|退房 4|删除
     * @var
     */
    private $type;

    /**
     * 原房间。用于日志记录
     * @var array
     */
    private $oldRooms = [];

    /**
     * 调整后的新房间。用于日志记录
     * @var array
     */
    private $newRooms = [];

    /**
     * 构造函数
     */
    public function __construct()
    {
        //dd(Route::current()->getActionName());
        $this->middleware('my.auth');
        //使用中间件过滤字段
        $this->middleware('fieldFilter', ['only'=>['postStore']]);
    }

    /**
     * 首页
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        $companies = Company::where('is_quit', 0)->paginate(config('cbs.pageNumber'));
        $count = $this->companyCount();

        return view('company.index', ['companies'=>$companies, 'count'=>$count]);
    }

    private function companyCount($whereRaw = NULL)
    {
        $whereArr[] = 'is_quit=0';
        if ($whereRaw) {
            $whereArr[] = $whereRaw;
        }
        $companies = Company::whereRaw(implode(' and ', $whereArr))->get();
        $count = [];
        $count['company'] = count($companies);
        $count['livingRoom'] = $count['diningRoom'] = $count['serviceRoom'] = 0;
        $companyIdArray = [];
        foreach ($companies as $company) {
            $companyIdArray[] = $company->company_id;
        }
        $rooms = Room::whereIn('company_id', $companyIdArray)->get();
        foreach ($rooms as $room) {
            switch ($room->room_type) {
                case '1':
                    //计算居住房间个数
                    $count['livingRoomNumber'][$room->company_id] = isset($count['livingRoomNumber'][$room->company_id]) ?
                        $count['livingRoomNumber'][$room->company_id] +1 :
                        1;
                    $count['livingRoom']++;
                    break;
                case '2':
                    $count['diningRoom']++;
                    break;
                case '3':
                    $count['serviceRoom']++;
                    break;
            }
        }
        return $count;
    }

    /**
     * 搜索 只能同时搜索公司名或者人名
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function getSearch (Request $request)
    {
        $companyName = trim(strip_tags(htmlspecialchars($request->company_name)));
        $personName = trim(strip_tags(htmlspecialchars($request->person_name)));

        $whereArr = ['is_quit=0'];
        $company = new Company();
        if (!empty($companyName)) {
            $whereArr[] = 'company_name like "%'.$companyName.'%"';
        } elseif (!empty($personName)) {
            $whereArr[] = '(linkman like "%'.$personName.'%" or manager like "%'.$personName.'%")';
        }
        $whereStr = implode(' and ', $whereArr);
        $companies = Company::whereRaw($whereStr)->paginate(config('cbs.pageNumber'));
        //导出文件
        if ($request->is_export == 1) {
            ExcelController::exportCompanies($companies);
            return response()->redirectTo('company/index');
        }

        $count = $this->companyCount($whereStr);
        return view('company.index', ['companies'=>$companies, 'count'=>$count]);
    }

    /**
     * 添加公司
     * @return \Illuminate\View\View
     */
    public function getAdd()
    {
        return view('company.addBasicInfo');
    }

    public function getEdit($companyId)
    {
        //验证company_id的合法性
        $this->validateCompanyId($companyId);

        $company = Company::findOrFail((int)$companyId);

        return view('company/edit', ['company'=>$company]);
    }

    /**
     * 存储公司基本数据
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function postStoreBasicInfo(Request $request)
    {
        //字段验证
        $validator = Validator::make($request->all(), [
            'company_id'=>'integer|min:1',
            'company_name' => 'required|between:1,255',
            'company_description'=>'between:1,255',
            'linkman'=>'required|between:1,5',
            'linkman_tel'=>'numeric',
            'manager'=>'between:1,5',
            'manager_tel'=>'numeric',
            'company_remark'=>'between:1,255',
            'type'=>'integer|min:1|max:3'
        ]);
        //验证不通过，返回第一个错误信息
        if ($validator->fails()) {
            exit(json_encode(['message'=>$validator->errors()->first(), 'status'=>0]));
        }

        $company = new Company();
        $company->company_name = $request->company_name;
        $company->company_description = $request->company_description;
        $company->linkman = $request->linkman;
        $company->linkman_tel = $request->linkman_tel;
        $company->manager = $request->manager;
        $company->manager_tel = $request->manager_tel;
        $company->company_remark = $request->company_remark;
        //开启事务
        DB::beginTransaction();
        if ($company->save()) {
            //主键
            $companyId = $company->getKey();
            //提交事务
            DB::commit();
            return $this->getSelectRooms($companyId);
        } else {
            //错误，回滚事务
            DB::rollBack();
            //TODO  好好研究一下response  重构一下302 等问题
            return response()->redirectTo(url('common/302'));
        }
    }
    public function postStoreEditInfo(Request $request)
    {
        //字段验证
        $validator = Validator::make($request->all(), [
            'company_id'=>'required|integer|min:1',
            'company_name' => 'required|between:1,255',
            'company_description'=>'between:1,255',
            'linkman'=>'required|between:1,5',
            'linkman_tel'=>'numeric',
            'manager'=>'between:1,5',
            'manager_tel'=>'numeric',
            'company_remark'=>'between:1,255',
            'type'=>'integer|min:1|max:3'
        ]);
        //验证不通过，返回第一个错误信息
        if ($validator->fails()) {
            exit(json_encode(['message'=>$validator->errors()->first(), 'status'=>0]));
        }

        $company = Company::find($request->company_id);
        if ($company) {
            $company->company_name = $request->company_name;
            $company->company_description = $request->company_description;
            $company->linkman = $request->linkman;
            $company->linkman_tel = $request->linkman_tel;
            $company->manager = $request->manager;
            $company->manager_tel = $request->manager_tel;
            $company->company_remark = $request->company_remark;
            //开启事务
            DB::beginTransaction();
            if ($company->save()) {
                //提交事务
                DB::commit();
                return response()->json(['message'=>'操作成功！', 'status'=>1]);
            } else {
                //错误，回滚事务
                DB::rollBack();
                //TODO  好好研究一下response  重构一下302 等问题
                return response()->json(['message'=>'失败：请重试！', 'status'=>0]);;
            }
        }

    }

    /**
     * 公司入住时选择房间
     * @param $companyId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getSelectRooms($companyId)
    {
        if (!$companyId) {
            return response()->redirectTo(url('common/302'));
        }
        return view('company.selectRooms',['company_id'=>$companyId]);
    }

    /**
     * 存储已经选好的房间
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postStoreSelectedRooms(Request $request)
    {
        //roomIds格式: 1_2_3
        //roomTypes格式 : 1_1_2|2_1_2|3_2_1
        $roomIds = htmlspecialchars(strip_tags($request->roomIds));
        $roomAndTypes = htmlspecialchars(strip_tags($request->roomTypes));
        $companyId = intval($request->company_id);


        $roomIdArr = explode('_', $roomIds);
        $roomTypeArr = explode('|', $roomAndTypes);

        //旧房间
        $oldRooms = Room::where('company_id', $companyId)->get();
        // 清除所有旧房间
        Room::where('company_id', $companyId)->update([
            'company_id'=>0,
            'rent_type_id'=>1,
            'gender'=>1
        ]);

        //修改房间表
        Room::whereIn('room_id', $roomIdArr)
            ->update(['company_id' => $companyId]);
        foreach ($roomTypeArr as $value) {
            $currentArr = explode('_', $value);
            Room::where('room_id', intval($currentArr[0]))
                ->update([
                    'rent_type_id'=>intval($currentArr[1]),
                    'gender'=>intval($currentArr[2])
                ]);
        }
        $newRooms = Room::whereIn('room_id', $roomIdArr)->get();

        //记录改动日志
        if ($request->is_edit) {// 修改
            $this->type = 2;
            if (!empty($oldRooms)) {
                foreach ($oldRooms as $oldRoom) {
                    $this->oldRooms[] = $oldRoom->room_name;
                }
            }
        } else { // 入住
            $this->type = 1;
            $this->oldRooms = [];
        }
        if (!empty($newRooms)) {
            foreach ($newRooms as $newRoom) {
                $this->newRooms[] = $newRoom->room_name;
            }
        }
        CompanyLogController::log($this->type, $companyId, $this->oldRooms, $this->newRooms);

        return response()->json(['message'=>'操作成功！', 'status'=>1]);
    }

    /**
     * 指定公司明细
     * @param $companyId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCompanyDetail($companyId)
    {
        $companyDetail = [];
        $companyId = intval($companyId);
        $company = Company::where('company_id', $companyId)
            ->where('is_quit', 0)
            ->first();

        $companyDetail['name'] = $company->company_name;
        $companyDetail['description'] = $company->company_description;
        $companyDetail['link'] = $company->linkman;
        $companyDetail['link_tel'] = $company->linkman_tel;
        $companyDetail['manager'] = $company->manager;
        $companyDetail['manager_tel'] = $company->manager_tel;
        $companyDetail['remark'] = $company->company_remark;
        $companyDetail['created_at'] = $company->created_at;
        $companyDetail['livingRoom'] = [];
        $companyDetail['diningRoom'] = '';
        $companyDetail['serviceRoom'] = '';
        $companyDetail['count']['livingRoomNumber'] = 0;
        $companyDetail['count']['livingPersonNumber'] = 0;
        $companyDetail['count']['diningRoomNumber'] = 0;
        $companyDetail['count']['serviceRoomNumber'] = 0;

        $rooms = Room::where('company_id', $companyId)->get();
        $allRentType = $rentType = DB::table('rent_type')->get();
        foreach ($allRentType as $rentType) {
            $rentTypeArr[$rentType->rent_type_id] = $rentType->person_number;
        }
        foreach ($rooms as $room) {
            switch (intval($room->room_type)){
                case 1:
                    if (isset($rentTypeArr[$room->rent_type_id])) {
                        $companyDetail['count']['livingPersonNumber'] += $rentTypeArr[$room->rent_type_id];
                        $companyDetail['count']['livingRoomNumber'] += 1;
                        // $rentType['rent_type_id'] = 'person_number';
                        if (isset($companyDetail['livingRoom'][$rentTypeArr[$room->rent_type_id]])) {
                            $companyDetail['count'][$rentTypeArr[$room->rent_type_id]]++;
                            $companyDetail['livingRoom'][$rentTypeArr[$room->rent_type_id]] .= $room->room_name . '&nbsp;&nbsp;&nbsp;';
                        } else {
                            $companyDetail['count'][$rentTypeArr[$room->rent_type_id]] = 1;
                            $companyDetail['livingRoom'][$rentTypeArr[$room->rent_type_id]] = $room->room_name . '&nbsp;&nbsp;&nbsp;';
                        }
                    }
                    break;
                case 2:
                    $companyDetail['diningRoom'] .= $room->room_name . '&nbsp;&nbsp;&nbsp;';
                    $companyDetail['count']['diningRoomNumber']++;
                    break;
                case 3:
                    $companyDetail['serviceRoom'] .= $room->room_name . '&nbsp;&nbsp;&nbsp;';
                    $companyDetail['count']['serviceRoomNumber']++;
                    break;
            }
        }
        return view('company.companyDetail', ['companyDetail'=>$companyDetail]);
    }

    /**
     * 显示公司尚未缴费的房间明细
     * @param $companyId
     * @return \Illuminate\View\View
     */
    public function getCompanyUtility($companyId)
    {
        $this->validateCompanyId($companyId);
        $utilities = DB::table('utility')
            ->join('company', 'company.company_id', '=', 'utility.company_id')
            ->join('room', 'room.room_id', '=', 'utility.room_id')
            ->where('utility.company_id', $companyId)
            ->where('is_charged', 0)
            ->get();

        $companyName = DB::table('utility')
            ->join('company', 'company.company_id', '=', 'utility.company_id')
            ->where('utility.company_id', $companyId)
            ->value('company_name');
        $count = [];
        $dateArr = [];

        if (count($utilities) > 0) {
            $count['water_money'] = $count['electric_money'] = 0;
            foreach ($utilities as $utility) {
                $dateArr[] = $utility->year . '-' . $utility->month;
                $count['water_money'] += $utility->water_money;
                $count['electric_money'] += $utility->electric_money;
            }
            $dateArr = array_unique($dateArr);
        }
        return view(
            'company.charge',
            [
                'utilities'=>$utilities,
                'date'=>implode('、', $dateArr),
                'count'=>$count,
                'company_id'=>$companyId,
                'company_name'=>$companyName
            ]
        );
    }

    /**
     * 公司所属房间批量缴费
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postCompanyUtilityCharge(Request $request)
    {
        $companyId = intval($request->company_id);
        if (!$companyId) {
            return response()->json(['message'=>'错误：参数错误！', 'status'=>0]);
        }
        $utilityIds = DB::table('utility')
            ->join('company', 'company.company_id', '=', 'utility.company_id')
            ->join('room', 'room.room_id', '=', 'utility.room_id')
            ->where('utility.company_id', $companyId)
            ->where('is_charged', 0)
            ->lists('utility_id');

        if (UtilityController::chargeStore($utilityIds)) {
            return response()->json(['message'=>'操作成功！', 'status'=>1]);
        }
        return response()->json(['message'=>'错误：请重试...', 'status'=>0]);
    }

    /**
     * 调整房间
     * @param $companyId
     * @return \Illuminate\View\View
     */
    public function getChangeRooms($companyId)
    {
        //验证company_id的合法性
        $this->validateCompanyId($companyId);

        $rooms = Room::where('company_id', (int)$companyId)
            ->get();
        $livingRooms = $diningRooms = $serviceRooms =[];
        foreach ($rooms as $room) {
            switch ($room->room_type) {
                case '1':
                    $livingRooms[] = $room;
                    break;
                case '2':
                    $diningRooms[] = $room;
                    break;
                case '3':
                    $serviceRooms[] = $room;
                    break;
            }
        }
        $company = Company::find($companyId);

        return view('company/changeRooms', [
            'livingRooms'=>$livingRooms,
            'diningRooms'=>$diningRooms,
            'serviceRooms'=>$serviceRooms,
            'company'=>$company]);
    }

    public function getQuit(Request $request)
    {
        //有未交水电
        if (DB::table('utility')
            ->where('company_id', $request->delete_id)
            ->where('is_charged', 0)
            ->count() > 0) {
            return response()->json(['message'=>'此公司有未缴水电费，无法退租！', 'status'=>0]);
        }

        $company = Company::find($request->delete_id);
        $company->is_quit = 1;
        $company->save();
        return response()->json(['message'=>'操作成功！', 'status'=>1]);
    }

    private function validateCompanyId($companyId)
    {
        $companyId = (int) $companyId;
        if (!$companyId) {
            exit("<h2>参数错误</h2>");
        }
    }

}