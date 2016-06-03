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
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getChargedList()
    {
        return 'charged';
    }


    public function getUnchargedList()
    {
        return 'uncharged';
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function getCreate($companyId)
    {
        $companyId = intval($companyId);
        if (!is_int($companyId)) exit;

        $company = Company::find($companyId);
        return view('punish.create', ['company'=>$company]);
    }

    /**
     * Store a newly created resource in storage.
     *
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
