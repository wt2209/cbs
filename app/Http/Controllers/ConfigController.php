<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        ]);
    }

    public function postStore(Request $request)
    {
        $this->validate($request, [
            'page_number' => 'required|integer',
            'precision' => 'required|integer',
            'electric_money' => 'required|numeric',
            'water_money' => 'required|numeric',
        ]);

        $config = '<?php';

        $config .= " return ['pageNumber'=>'{$request->page_number}',";
        $config .= "'precision'=>'{$request->precision}',";
        $config .= "'electricMoney'=>'{$request->electric_money}',";
        $config .= "'waterMoney'=>'{$request->water_money}',";
        $config .= "];";

        //Storage::delete(config_path('cbs.php'));
        if (is_file(config_path('cbs.php'))) {
            unlink(config_path('cbs.php'));
        }

        if (file_put_contents(config_path('cbs.php'), $config)){
            return response()->json(['message'=>'操作成功！', 'status'=>1]);
        } else {
            return response()->json(['message'=>'操作成功！', 'status'=>0]);
        }
    }
}
