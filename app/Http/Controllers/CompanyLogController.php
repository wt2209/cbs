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
    static public function log($type, $companyId, $oldRooms=[], $newRooms=[])
    {
        $type = intval($type);

        if ($type >=1 && $type <= 4) {
            //根据公司id查找公司名
            $company = Company::select('company_name')
                ->find((int)$companyId);
            //错误处理
            if (!$company) {
                return false;
            }

            //新建日志模型
            //TODO user_id

            $companyLog = new CompanyLog();
            $companyLog->type = $type;
            $companyLog->company_name = $company->company_name;
            $companyLog->old_rooms = empty($oldRooms) ? '' : implode('|', $oldRooms);
            $companyLog->new_rooms = empty($newRooms) ? '' : implode('|', $newRooms);
            $companyLog->save();
            /*
             * 使用下面这种方式时间戳不会更新，应该是适用于精确的更新
             * CompanyLog::insert([
                'type'=>$type,
                'company_name'=>$company->company_name,
                'old_rooms'=>empty($oldRooms) ? '' : implode('|', $oldRooms),
                'new_rooms'=>empty($newRooms) ? '' : implode('|', $newRooms)
            ]);*/
        } else {
            return false;
        }
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