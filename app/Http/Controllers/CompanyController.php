<?php

namespace App\Http\Controllers;

use DB;
use App\Model\Company;
use App\Model\Room;
use Illuminate\Http\Request;
use App\Http\Controllers\CompanyLogController;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

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
        //使用中间件过滤字段
        $this->middleware('fieldFilter', ['only'=>['postStore']]);
    }

    /**
     * 首页
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        $companies = Company::all();
        $count['company'] = count($companies);
        $count['room'] = 0;
        foreach ($companies as $company) {
            $count['room'] += count($company->rooms);
        }
        return view('company.index', ['companies'=>$companies, 'count'=>$count]);
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

        $company = new Company();
        if (!empty($companyName)) {
            $companies = $company->where('company_name', 'like', '%' . $companyName . '%')->get();
        } elseif (!empty($personName)) {
            $companies = $company->where("linkman", 'like', '%' . $personName . '%')
                ->orWhere('manager', 'like', '%' . $personName . '%')
                ->get();
        } else {
            $companies = Company::all();
        }
        $count['company'] = count($companies);
        $count['room'] = 0;
        foreach ($companies as $company) {
            $count['room'] += count($company->rooms);
        }
        return view('company.index', ['companies'=>$companies, 'count'=>$count]);
    }

    /**
     * 添加公司
     * @return \Illuminate\View\View
     */
    public function getAdd()
    {
        return view('company.add');
    }

    public function getEdit($companyId)
    {
        //验证company_id的合法性
        $this->validateCompanyId($companyId);

        $company = Company::findOrFail((int)$companyId);

        return view('company/edit', ['company'=>$company]);
    }

    /**
     * 存储数据
     * @param Request $request
     */
    public function postStore(Request $request)
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

        //新建实例，根据是否有companyId判断是修改还是新增
        if ($request->company_id) { // 存在company_id，修改数据。由于是魔术方法，因此不能使用isset($request->room_id)
            $company = Company::findOrFail($request->company_id);//会自动进行错误处理
        } else { //新增数据
            //入住
            $this->type = 1;
            $company = new Company();
        }

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
            $request->company_id = $company->getKey();
            if ($this->setCompanyRoom($request)) {
                //提交事务
                DB::commit();
            } else {
                //错误，回滚事务
                DB::rollBack();
                exit(json_encode(['message'=>'失败：数据添加失败，请重试...', 'status'=>0]));
            }
            exit(json_encode(['message'=>'操作成功！', 'status'=>1]));
        } else {
            //错误，回滚事务
            DB::rollBack();
            exit(json_encode(['message'=>'失败：数据添加失败，请重试...', 'status'=>0]));
        }
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
            ->select('building', 'room_number')
            ->get();

        $company = Company::find($companyId);

        return view('company/changeRooms', ['rooms'=>$rooms, 'company'=>$company]);
    }

    /**
     * 存储调整后的房间，并对操作进行记录
     * @param Request $request
     */
    public function postChangeRoomsStore(Request $request)
    {
        if (Company::where('company_id', (int)($request->company_id))->count() > 0) {
            //更改房间
            $this->type = 2;
            if ($request->old_rooms) {
                foreach ($request->old_rooms as $oldRoom) {
                    $this->oldRooms[] = addslashes(htmlspecialchars(strip_tags(trim($oldRoom))));
                }
                //清除公司原有房间的信息
                $this->removeOldRooms();
            }

            if ($this->setCompanyRoom($request)) {
                exit(json_encode(['message'=>'操作成功！', 'status'=>1]));
            } else {
                exit(json_encode(['message'=>'失败：数据添加失败，请重试...', 'status'=>0]));
            }
        } else {
            exit("<h2>参数错误</h2>");
        }
    }

    private function validateCompanyId($companyId)
    {
        $companyId = (int) $companyId;
        if (!$companyId) {
            exit("<h2>参数错误</h2>");
        }
    }

    /**
     * 清除公司原有房间的信息
     */
    private function removeOldRooms()
    {
        foreach ($this->oldRooms as $oldRoom) {
            $arr = explode('-', $oldRoom);
            if (count($arr) != 2) {
                continue;
            }
            Room::where('building', $arr[0])
                ->where('room_number', $arr[1])
                ->update(['company_id'=>0]);
        }
    }

    /**
     * 设置公司与房间的映射，并存储到房间表
     * @param $request
     * @param $companyId
     * @return bool
     */
    private function setCompanyRoom($request)
    {
        //不存在添加房间的过程
        if (!$request->add_room_type) {
            return true;
        }
        //要修改的房间id数据，形式为 [6,7,8]
        $data = [];
        $postRooms = [];

        //获取空房间名与id的映射
        $roomToId = $this->setRoomToId();
        if ($request->add_room_type == 1) { //手动输入
            //处理提交过来的房间数据
            $input = str_replace('　', ' ', $request->room_input);
            $postRooms = explode(' ', $input);
        } else if ($request->add_room_type == 2) { //从空房间选择
            $postRooms = $request->room_select;
        }

        if (!empty($postRooms)) {
            foreach ($postRooms as $postRoom) {
                //过滤空数据
                $postRoom = trim($postRoom);
                if (empty($postRoom)) {
                    continue;
                }

                if (isset($roomToId[$postRoom])) {
                    $this->newRooms[] = $postRoom;
                    $data[] = $roomToId[$postRoom];
                }
            }
        }

        //没有选中房间。默认返回true
        if (empty($data)) {
            return true;
        }

        //记录房间更改日志
        CompanyLogController::log($this->type, (int)$request->company_id, $this->oldRooms, $this->newRooms);
        //修改房间表
        Room::whereIn('room_id', $data)
            ->update(['company_id' => $request->company_id]);
        return true;
    }

    /**
     * 获取空房间号与房间id的映射
     * @return array
     */
    private function setRoomToId()
    {
        if ($this->type == 1) { //因为是新增公司，所以只需要空房间
            $rooms = Room::where('company_id', 0)
                        ->select('room_id', 'building', 'room_number')
                        ->get();
        } else if ($this->type == 2) { //修改房间，需要原来的房间，因此查找全部房间
            $rooms = Room::select('room_id', 'building', 'room_number')
                        ->get();
        } else {
            exit("<h2>参数错误</h2>");
        }
        $roomToId = []; // 房间号与房间id的映射
        foreach ($rooms as $room) {
            $key = $room->building . '-' . $room->room_number;
            $roomToId[$key] = $room->room_id;
        }
        return $roomToId;
    }
}