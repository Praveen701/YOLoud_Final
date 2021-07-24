<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstagramSocial extends Model
{
    protected $fillable = [
        'iusername','iaudienceloc','iaudienceage','iaudiencegen','ifollowers','iposts','iavglike','iavgcmt','iengagementrate','iqs','ipfr'
    ];
    public function campaignlinked(){
        return $this->hasMany('App\CampaignInflList','iid','iid');
    }
    
}
