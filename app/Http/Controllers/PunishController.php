<?php

namespace App\Http\Controllers;

use DB;
use App\Model\Punish;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Model\Company;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class PunishController extends Controller
{
    /**
     * 已缴费列表
     * @return \Illuminate\View\View
     */
    public function getChargedList()
    {
        return 'charged';
    }

    /**
     * 未缴费列表
     * @return \Illuminate\View\View
     */
    public function getUnchargedList()
    {
        //TODO 需要处理user_id 和cancel_user_id
        //TODO 分页
        $unchargedLists = Punish::where('is_charged', 0)
            ->paginate(2);
        $count = $this->setUnchargedCount();
        return view('punish.uncharged', ['unchargedLists'=>$unchargedLists, 'count'=>$count]);
    }


    public function getCharge(Request $request)
    {
        $punishId = intval($request->charge_id);
        if (!$punishId) {
            return response()->json(['message'=>"错误：请正确操作！", 'status'=>0]);
        }

        if ($this->chargeStore($punishId)) {
            return response()->json(['message'=>"操作成功！", 'status'=>1]);
        }
        return response()->json(['message'=>"错误：请重试！", 'status'=>0]);
    }
    /**
     * 开罚单
     * @param $companyId
     * @return \Illuminate\View\View
     */
    public function getCreate($companyId)
    {
        $companyId = intval($companyId);
        if (!is_int($companyId)) exit;

        $company = Company::find($companyId);
        return view('punish.create', ['company'=>$company]);
    }

    /**
     *存储罚单
     * @param  Request  $request
     * @return Response
     */
    public function postStore(Request $request)
    {
        //字段验证
        $validator = Validator::make($request->all(), [
            'company_id'=>'integer|min:1',
            'money'=>'required|numeric',
            'reason'=>'required|between:1,255',
            'punish_remark'=>'between:1,255'
        ]);

        //验证不通过，返回第一个错误信息
        if ($validator->fails()) {
            return response()->json(['message'=>$validator->errors()->first(), 'status'=>0]);
        }

        $punish = new Punish();
        $punish->company_id = $request->company_id;
        //TODO user_id 需要处理
        //$punish->user_id = $request->company_id;
        $punish->money = $request->money;
        $punish->reason = $request->reason;
        $punish->punish_remark = $request->punish_remark;
        $punish->created_at = strtotime($request->created_at) ?
                                    date('Y-m-d H:i:s', strtotime($request->created_at)) :
                                    date('Y-m-d H:i:s');

        //开启事务
        DB::beginTransaction();
        if ($punish->save()) {
            DB::commit();
            return response()->json(['message'=>'操作成功！', 'status'=>1]);
        } else {
            //错误，回滚事务
            DB::rollBack();
            return response()->json(['message'=>'失败：数据添加失败，请重试...', 'status'=>0]);
        }
    }


    /**
     * 修改备注
     * @param $punishId
     * @return \Illuminate\View\View
     */
    public function getEditRemark($punishId)
    {
        if (!intval($punishId)) {
            exit('非法请求！');
        }
        $punish = Punish::find(intval($punishId));
        return view('punish.editRemark', ['punish'=>$punish]);
    }

    /**
     * 存储备注
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postUpdateRemark(Request $request)
    {
        $punishId = intval($request->punish_id);
        $remark = trim(htmlspecialchars(strip_tags($request->punish_remark)));
        if ($punishId) {
            DB::table('punish')->where('punish_id', $punishId)
                ->update(['punish_remark' => $remark]);
            return response()->json(['message'=>"操作成功！",'status'=>1]);
        }
        return response()->json(['message'=>"失败：请重试！",'status'=>0]);
    }


    public function getCancel($punishId)
    {
        return view('punish.cancel');
    }


    /**
     * 计算未缴费的统计信息
     * @param null $where
     * @return array
     */
    private function setUnchargedCount($where = NULL)
    {
        $whereArr[] = 'is_charged = 0';
        if ($where) {
            $whereArr[] = $where;
        }
        $whereStr = implode(' and ', $whereArr);

        $count['totalNumber'] = DB::table('punish')->whereRaw($whereStr)
                                    ->count();
        $count['totalMoney'] = DB::table('punish')->whereRaw($whereStr)
                                    ->sum('money');
        return $count;
    }

    /**
     * 存储缴费记录
     * @param $punishId
     * @return mixed
     */
    private function chargeStore($punishId)
    {
        return DB::table('punish')
            ->where('punish_id', $punishId)
            ->update([
                'is_charged'=>1,
                'charged_at'=>date('Y-m-d H:i:s')
            ]);
    }
}
