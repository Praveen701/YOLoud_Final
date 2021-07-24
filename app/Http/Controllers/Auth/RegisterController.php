<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Influencer;


use App\InstagramSocial;
use App\YFTSocials;
use App\Providers\RouteServiceProvider;
use App\User;
use App\Mail\AccountCreatedSuccessfully;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['accepted'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user=User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'type'=>0,
            'profilestatus'=>0
        ]);
        $x=[];
        $x['type']='Bank Account Transfer';
        $y=[];
        $y['accname']='';
        $y['accno']='';
        $y['ifsc']='';
        $x['status']=0;
        $x['value']=json_encode($y);


        $p=[];
        $p['type']='Working Professional';
        $z=[];
        $z['compname']='';
        $z['designation']='';
        $p['status']=0;
        $p['value']=json_encode($z);


        $inf=new Influencer;
        $inf->iid=$user->id;
        $inf->payment=json_encode($x);
        $inf->occupation=json_encode($p);
        $inf->save();
        
      
    
        $insta=new InstagramSocial;
        $insta->iid=$user->id;
        $insta->save();

        $yft=new YFTSocials;
        $yft->iid=$user->id;
        $yft->save();

        Mail::to($user->email)->send(new AccountCreatedSuccessfully($user));
        return $user;
        
    }
}
