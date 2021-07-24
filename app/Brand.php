<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Brand extends Model
{

    protected $fillable = [
        'name', 'email', 'companyname','designation','city','state','country','pincode','phonenumber'.'offering','phonestatus','emailstatus'
    ];

    public function campaigncreated(){
        return $this->hasMany('App\Campaign','iid','id');
    }
    public function brandl(){
        return $this->hasOne('App\CampaignInflList','bid','id');
    }
  


}
