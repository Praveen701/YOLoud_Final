<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email','password','type','profilestatus','UID','notification_preference','accountstatus','terms'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function influencer(){
        return $this->hasOne('App\Influencer','iid','id');
    }
    public function yftsocial(){
        return $this->hasOne('App\YFTSocials','iid','id');
    }
    public function brands(){
        return $this->hasOne('App\Brand','iid','id');
    }
    public function facebook(){
        return $this->hasOne('App\FacebookSocial','iid','id');
    }
    public function instagram(){
        return $this->hasOne('App\InstagramSocial','iid','id');
    }
  
    public function youtube(){
        return $this->hasOne('App\YoutubeSocial','iid','id');
    }
    public function influencers(){
        return $this->hasMany('App\Influencer','iid','id');
    }
    public function instagrams(){
        return $this->hasMany('App\InstagramSocial','iid','id');
    }
}
