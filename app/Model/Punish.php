<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class Punish extends Model
{
    //表名
    protected $table='punish';

    //主键
    protected $primaryKey = 'punish_id';

    /**
     * 与公司一对多的关系
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function company()
    {
        return $this->belongsTo('App\Model\Company', 'company_id');
    }

    //TODO user 与cancel_user 是否能通过这种方式解决？
    /*public function user()
    {
        return $this->belongsTo('App\Model\Company', 'company_id');
    }

    public function cancelUser()
    {
        return $this->belongsTo('App\Model\Company', 'company_id');
    }*/

}
