<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Campaign extends Model

{
    public function influencerlinked(){
        return $this->hasMany('App\CampaignInflList','cid','id')->where('iid',Auth::user()->id);
    }
   
    
    public function brandlinked(){
        return $this->hasMany('App\Brand','id','iid');
    }
    public function brand(){
        return $this->hasOne('App\Brand','id','iid');
    }
  
  
   
   

}
