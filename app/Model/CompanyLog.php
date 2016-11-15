<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CompanyLog extends Model
{
    //表名
    protected $table='company_log';

    //主键
    protected $primaryKey = 'cl_id';

    //记录与承包商公司一对多关系
    public function Company()
    {
        return $this->belongsTo('App\Model\Company', 'company_id', 'company_id');
    }

    //记录与房间一对多关系
    public function Room()
    {
        return $this->belongsTo('App\Model\Room', 'room_id', 'room_id');
    }

    //记录与房间一对多关系
    public function User()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
