<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model\Room;

class PersonController extends Controller
{
    public function index()
    {
        $data = Room::all();
        echo '<pre>';
        print_r($data);die;
        return view('person');
    }
}