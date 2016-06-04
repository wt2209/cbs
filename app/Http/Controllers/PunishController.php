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
        //TODO 需要处理user_id 和cancel_user_id
        //TODO 分页
        $chargedLists = Punish::where('is_charged', 1)
            ->where('is_canceled', 0)
            ->paginate(2);
        $count = $this->setCountMessage('charged');
        return view('punish.charged', ['chargedLists'=>$chargedLists, 'count'=>$count]);
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
            ->where('is_canceled', 0)
            ->paginate(2);
        $count = $this->setCountMessage('uncharged');
        return view('punish.uncharged', ['unchargedLists'=>$unchargedLists, 'count'=>$count]);
    }

    /**
     * 撤销列表
     * @return \Illuminate\View\View
     */
    public function getCanceledList()
    {
        //TODO 需要处理user_id 和cancel_user_id
        //TODO 分页
        $canceledLists = Punish::where('is_canceled', 1)
            ->paginate(2);
        $count = $this->setCountMessage('canceled');
        return view('punish.canceled', ['canceledLists'=>$canceledLists, 'count'=>$count]);
    }


    public function getChargedSearch(Request $request)
    {

    }
    public function getUnchargedSearch(Request $request)
    {

    }
    public function getCancelSearch(Request $request)
    {

    }

    /**
     * 缴费
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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
     * 存储罚单
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
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

    /**
     * 撤销罚单
     * @param $punishId
     * @return \Illuminate\View\View
     */
    public function getCancel($punishId)
    {
        if (!intval($punishId)) {
            exit('非法请求！');
        }
        $punish = Punish::find(intval($punishId));
        return view('punish.cancel',['punish'=>$punish]);
    }

    /**
     * 存储撤销相关的数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postUpdateCancel(Request $request)
    {
        $punishId = intval($request->punish_id);
        $cancelReason = trim(htmlspecialchars(strip_tags($request->cancel_reason)));
        //TODO cancel_user_id
        $cancelUserId = 0;

        //必须有撤销原因
        if (empty($cancelReason)) {
            return response()->json(['message'=>"失败：请填写撤销原因！", 'status'=>0]);
        }
        //已缴费的项目不能撤销
        $punish = DB::table('punish')->find($punishId);
        if (intval($punish->is_charged) === 1 ) {
            return response()->json(['message'=>"失败：已缴费罚单不能撤销！", 'status'=>0]);
        }

        if (DB::table('punish')->where('punish_id', $punishId)
                ->update([
                    'cancel_user_id'    =>  $cancelUserId,
                    'cancel_reason'     =>  $cancelReason,
                    'is_canceled'       =>  1,
                    'cancel_at'         =>  date('Y-m-d H:i:s')
            ])) {
            return response()->json(['message'=>"操作成功！", 'status'=>1]);
        }
        return response()->json(['message'=>"失败：内部错误，请重试！", 'status'=>0]);
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

    /**
     * 计算统计信息
     * @param null $where
     * @return array
     */
    private function setCountMessage($type, $where = NULL)
    {
        $whereArr = [];
        switch ($type) {
            case 'charged':
                $whereArr[] = 'is_charged = 1';
                $whereArr[] = 'is_canceled = 0';//未被撤销的
                break;
            case 'uncharged':
                $whereArr[] = 'is_charged = 0';
                $whereArr[] = 'is_canceled = 0';//未被撤销的
                break;
            case 'canceled':
                $whereArr[] = 'is_canceled = 1';//被撤销的
                break;
        }

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
}
