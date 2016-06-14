<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RentType extends Model
{
    //表名
    protected $table='rent_type';

    //主键
    protected $primaryKey = 'rent_type_id';
}
