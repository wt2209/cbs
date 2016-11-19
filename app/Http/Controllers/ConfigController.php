<?php

namespace App\Http\Controllers;

use App\Model\RentType;
use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {
        return view('config/index', [
            'pageNumber'=>config('cbs.pageNumber'),
            'precision'=>config('cbs.precision'),
            'electricMoney'=>config('cbs.electricMoney'),
            'waterMoney'=>config('cbs.waterMoney'),
            'electricMax'=>config('cbs.electricMax'),
            'waterMax'=>config('cbs.waterMax'),
        ]);
    }

    public function postStore(Request $request)
    {
        $this->validate($request, [
            'page_number' => 'required|integer',
            'precision' => 'required|integer',
            'electric_money' => 'required|numeric',
            'water_money' => 'required|numeric',
            'electric_max' => 'required|numeric',
            'water_max' => 'required|numeric',
        ]);

        $config = '<?php';
        $config .= " return ['pageNumber'=>'{$request->page_number}',";
        $config .= "'precision'=>'{$request->precision}',";
        $config .= "'electricMoney'=>'{$request->electric_money}',";
        $config .= "'waterMoney'=>'{$request->water_money}',";
        $config .= "'electricMax'=>'{$request->electric_max}',";
        $config .= "'waterMax'=>'{$request->water_max}',";
        $config .= "];";

        if (is_file(config_path('cbs.php'))) {
            unlink(config_path('cbs.php'));
        }

        if (file_put_contents(config_path('cbs.php'), $config)){
            return response()->json(['message'=>'操作成功！', 'status'=>1]);
        } else {
            return response()->json(['message'=>'操作失败！', 'status'=>0]);
        }
    }

    public function getRentType()
    {
        $rentTypes = RentType::all();
        return view('config.rentType', ['rentTypes'=>$rentTypes]);
    }

    public function postStoreRentType(Request $request)
    {
        foreach ($request->rent_type_id as $k => $v) {
            if (!empty($request->person_number[$k]) && !empty($request->rent_money[$k])) {
                $data = [
                    'person_number'=>$request->person_number[$k],
                    'rent_money'=>$request->rent_money[$k]
                ];
                DB::table('rent_type')->where('rent_type_id', '=', $v)->update($data);
            }
        }
        return response()->json(['message'=>'操作成功！', 'status'=>1]);
    }
}
