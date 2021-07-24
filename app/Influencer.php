<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Influencer extends Model
{
    protected $fillable = [
        'phone', 'dob', 'gender','intrest','city','languages','PID','platform','primarycategory','intype'
    ];

    public function user(){
        return $this->hasOne('App\User','id','iid');
    }

  
    public function instagram(){
        return $this->hasOne('App\InstagramSocial','iid','id');
    }
    
    public function campaignlinked(){
        return $this->hasMany('App\CampaignInflList','iid','iid');
    }

 
  


}
