<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewInfluencer extends Model
{
    protected $fillable = [
       'name','email','phone', 'dob', 'gender','intrest','city','languages','instagram_username'
    ];
}
