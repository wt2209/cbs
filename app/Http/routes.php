<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');

//首页
Route::get('/', ['middleware'=>'auth', function () {
    return view('index');
}]);
Route::get('/home', ['middleware'=>'auth', function () {
    return view('index');
}]);

//欢迎页
Route::get('/welcome', function(){
    return view('welcome');
});
//欢迎页
Route::get('common/302', function(){
    return view('common/302');
});

//房间管理
Route::controller('room', 'RoomController');

//承包商公司管理
Route::controller('company', 'CompanyController');

//承包商公司房间变动记录
Route::controller('company-log', 'CompanyLogController');

//水电费处理
Route::controller('utility','UtilityController');

//罚款控制器
Route::controller('punish','PunishController');

//日程
Route::controller('calendar','CalendarController');

//excel
Route::controller('excel','ExcelController');