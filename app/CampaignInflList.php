<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CampaignInflList extends Model
{
    public function influencers(){
        return $this->hasMany('App\Influencer','iid','iid');
    }  
    public function user(){
        return $this->hasMany('App\User','id','iid');
    }  
    public function instagramusers(){
        return $this->hasMany('App\InstagramSocial','iid','iid');
    }  
    public function campaign(){
        return $this->hasOne('App\Campaign','cid','id');
    }
    public function brandlin(){
        return $this->hasOne('App\Brand','id','bid');
    }
    public function mcampaign(){
        return $this->hasMany('App\Campaign','id','cid');
    }
    public function suser(){
        return $this->hasOne('App\User','id','iid');
    }  
    public function instagramuser(){
        return $this->hasOne('App\InstagramSocial','iid','iid');
    }  
    public function influencer(){
        return $this->hasOne('App\Influencer','iid','iid');
    } 
    
}
