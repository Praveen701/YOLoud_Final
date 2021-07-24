<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Intrest;
use Validator;
use App\Brand;
use Str;
use File;
use Storage;
use App\Influencer;
use App\YFTSocials;
use App\Language;
use App\InstagramSocial;
use App\InfluencerLog;
use App\SocialLog;
use App\Campaign;
use App\CampaignInflList;
use Auth;
use App\Imports\InfluencersImport;
use App\Imports\BrandImport;
use App\Exports\InfluencerExport;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewInfluencer;
use Illuminate\Support\Facades\Notification;
use App\User;
use Excel;
use Hash;
class AdminController extends Controller
{
    public function homepage(){
        return view('Admin/home');
    }
  


    public function AddInfluencer(){
        $data=Intrest::select('id','name')->get();
        $language=Language::select('id','name')->get();
        $insta = InstagramSocial::select('id')->get();
        $yftsocial = YFTSocials::select('id')->get();
        return view('Admin/Influencer/add',['intrest'=>$data,'insta'=>$insta,'language'=>$language,'yftsocial'=>$yftsocial]);
    }
    public function ListInfluencer(Request $request)
    {
        $user = User::with('influencers')->with('instagrams')->where('type',0);
        if($request->ids){
            $data=Influencer::where('iid','LIKE','%'.$request->ids.'%')->pluck('id');
            $user=$user->whereIn('id',$data);
        }
        if((int)$request->id>0 && (int)$request->id<=2){
            if($request->id==1){
                $user=$user->orderBy('id','desc');
            }
            else{
                $user=$user->orderBy('id','asc');
            }
        }
        if($request->name){
            $user=$user->where('name','LIKE','%'.$request->name.'%');
        }
        if($request->email){
            $user=$user->where('email','LIKE','%'.$request->email.'%');
        }
        
        if($request->status && $request->status>=0){
            $user=$user->where('profilestatus',$request->status);
        }
        if($request->verified && $request->verified>=0){
            $user=$user->where('verified',$request->verified);
        }
        if($request->phone){
            $data=Influencer::where('phone','LIKE','%'.$request->phone.'%')->pluck('id');
            $user=$user->whereIn('id',$data);
        }
        
        
        if($request->type && $request->type>0){
            $data=Influencer::where('inftype',$request->type)->pluck('id');
            $user=$user->whereIn('id',$data);
        }
        if($request->category && $request->category>0){
            $data=Influencer::where('intrest','LIKE','%'.$request->category.'%')->pluck('id');
            $user=$user->whereIn('id',$data);
        }
        // return $user->get();        
        
        $page=20;
        if($request->rpp){
            $page=$request->rpp;
        }
        $user=$user->paginate($page);
     
        $cat=Intrest::select("name")->pluck('name');
        return view('Admin/Influencer/list',['user'=>$user,'cat'=>$cat]);
    }
   

    public function PendingInfluencer(Request $request)
    {
        $user = User::with('influencers')->with('instagrams')->where('type',0)->where('profilestatus',0);

       
        if($request->ids){
            $data=Influencer::where('iid','LIKE','%'.$request->ids.'%')->pluck('id');
            $user=$user->whereIn('id',$data);
        }
        if($request->name){
            $user=$user->where('name','LIKE','%'.$request->name.'%');
        }
        if($request->email){
            $user=$user->where('email','LIKE','%'.$request->email.'%');
        }
        
        if($request->phone){
            $data=Influencer::where('phone','LIKE','%'.$request->phone.'%')->pluck('id');
            $user=$user->whereIn('id',$data);
        }
        
        if($request->type && $request->type>0){
            $data=Influencer::where('inftype',$request->type)->pluck('id');
            $user=$user->whereIn('id',$data);
        }
        if($request->category && $request->category>0){
            $data=Influencer::where('intrest','LIKE','%'.$request->category.'%')->pluck('id');
            $user=$user->whereIn('id',$data);
        }
        // return $user->get();        
        
        $page=20;
        if($request->rpp){
            $page=$request->rpp;
        }
        $user=$user->paginate($page);
     
        $cat=Intrest::select("name")->pluck('name');
        return view('Admin/Influencer/pendinglist',['user'=>$user,'cat'=>$cat]);

      
    }

    
    public function ViewInfluencer($id)
    {
        $user = User::where('id',$id)->first();

        
        return view('Admin/Influencer/view',['data'=>$user]);
    }

    public function EditInfluencer($id)
    {
        $user = User::find($id);
       $intrest=Intrest::select('id','name')->get();
       $language=Language::select('id','name')->get();
        $influencer=Influencer::where('iid',$id)->first();
        $insta=InstagramSocial::where('iid',$id)->first();
        $yftsocial=YFTSocials::where('iid',$id)->first();
        $inflog=InfluencerLog::where('iid',$id)->get();
        $sociallog=SocialLog::where('iid',$id)->get();
        $camppar=CampaignInflList::where('iid',$id)->with('mcampaign')->get();

        // return $user;
        return view('Admin/Influencer/edit',['data'=>$user,'influencer'=>$influencer,'insta'=>$insta,'intrest'=>$intrest,
        'language'=>$language,'yftsocial'=>$yftsocial,'inflog'=>$inflog,'sociallog'=>$sociallog,'camppar'=>$camppar]);
   
   }



    public function StoreInfluencer(Request $request){
        // return $request;


        $user=new User;
        $user->name=$request->name;
        $user->email=$request->email;
        $user->password=Hash::make('YOLOUD001');

        $user->profilestatus=0;
        $user->accountstatus=0;

        $user->save();

        $influencer=new Influencer;
        $insta= new InstagramSocial;

        $influencer->iid=$user->id;
        $influencer->phone=$request->phone;
        $influencer->gender=$request->gender;
    
        $influencer->languages=$request->languages?json_encode($request->languages):null;
        $influencer->state=$request->state;
        $influencer->pincode=$request->pincode;
        $influencer->city=$request->city;
        $influencer->pincode=$request->pincode;
        $influencer->intrest=$request->intrest?json_encode($request->intrest):null;
        $influencer->dob=$request->dob;
        $influencer->country=$request->country;
       
      

        $x=[];
        $x['type']=$request->paytype;
        $x['value']=$request->payval;
        $influencer->payment=json_encode($x);
        $y=[];
        $y['type']=$request->occtype;
        $y['value']=$request->occval;
        $influencer->occupation=json_encode($y);
      

 
        $insta->iid=$user->id;
        $insta->iusername=$request->iusername;

        if ($request->ifollowers == '') {
            $insta->ifollowers= 0;
        }
        else {
            $insta->ifollowers=$request->ifollowers;
        }
        if ($request->iposts == '') {
            $insta->iposts= 0;
        }
        else {
            $insta->iposts=$request->iposts;
        }
        if ($request->iavglike == '') {
            $insta->iavglike= 0;
        }
        else {
            $insta->iavglike=$request->iavglike;
        }
        if ($request->iavgcmt == '') {
            $insta->iavgcmt= 0;
        }
        else {
            $insta->iavgcmt=$request->iavgcmt;
        }
    
        
        if($request->ifollowers <= 10000)
        {
            $influencer->inftype='Nano';
        }
        elseif ($request->ifollowers > 10000 && $request->ifollowers <= 100000) {
            $influencer->inftype='Micro';
        }
        elseif ($request->ifollowers > 100000 && $request->ifollowers <= 1000000) {
            $influencer->inftype='Macro';
        }
        else {
            $influencer->inftype='Mega';
        }

    
     
         if($insta->ifollowers != 0)
         {
       $insta->iengagementrate= ( round( (($insta->iavglike +  $insta->iavgcmt ) / $insta->ifollowers),2 ) *100 );

         }
         else {
             $insta->iengagementrate= 0;
         }


        
        $insta->iaudiencegen=$request->iaudiencegen;
        $insta->iaudienceage=$request->iaudienceage;
        $insta->iaudienceloc=$request->iaudienceloc;


        $yftsocial= new YFTSocials;
        $yftsocial->iid=$user->id;
        $yftsocial->yurl=$request->yurl;
        $yftsocial->furl=$request->furl;
        $yftsocial->tusername=$request->tusername;

        $yftsocial->save();
        $influencer->save();
        $insta->save();

        // Mail::to($request->email)->send(new NewInfluencer($user));

        $request->session()->flash('status', 'Added Succesfully');
        return redirect('/admin/influencer');


    }
    
    public function UpdateInfluencer(Request $request,$id)
    {
        // return $request;
        $user= User::find($id);

       
    
        
        if ($user->email == $request->email) //email
        {
            $user->email = $request->email;
        }
        else {
            $log=new InfluencerLog;
            $log->iid=$user->id;
            $log->des='Email changed from '.$user->email.' to '.$request->email;
            $log->save();
            $user->email = $request->email;
        }

        
        if ($user->name == $request->name) //name
        {
            $user->name = $request->name;
        }
        else {
            $log=new InfluencerLog;
            $log->iid=$user->id;
            $log->des='Name changed from '.$user->name.' to '.$request->name;
            $log->save();
            $user->name = $request->name;
        }

  
        $user->profilestatus=$request->profilestatus;
        $user->accountstatus=$request->accountstatus;
   
        $influencer=Influencer::where('iid',$user->id)->first();

        $influencer->gender=$request->gender;
        $influencer->intrest=$request->intrest?json_encode($request->intrest):null;
        $influencer->languages=$request->languages?json_encode($request->languages):null;
       

        $influencer->categorystatus = $request->categorystatus;
        $influencer->country=$request->country;

        if ($influencer->phone ==  $request->phone) //phone
        {
            $influencer->phone = $request->phone;
        }
        else {
            $log=new InfluencerLog;
            $log->iid=$user->id;
            $log->des='Phone Number changed from '.$influencer->phone.' to '.$request->phone;
            $log->save();
            $influencer->phone = $request->phone;
        }

        if ($influencer->dob == $request->dob) //DOB
         {
            $influencer->dob = $request->dob;
        }
        else {
            $log=new InfluencerLog;
            $log->iid=$user->id;
            $log->des='Date of Birth changed from '.$influencer->dob.' to '.$request->dob;
            $log->save();
            $influencer->dob = $request->dob;
        }
        if ($influencer->city == $request->city) //City
        {
           $influencer->city = $request->city;
       }
       else {
           $log=new InfluencerLog;
           $log->iid=$user->id;
           $log->des='City changed from '.$influencer->city.' to '.$request->city;
           $log->save();
           $influencer->city = $request->city;
       }
       if ($influencer->state == $request->state) //State
       {
          $influencer->state = $request->state;
      }
      else {
          $log=new InfluencerLog;
          $log->iid=$user->id;
          $log->des='State changed from '.$influencer->state.' to '.$request->state;
          $log->save();
          $influencer->state = $request->state;
      }

      if ($influencer->pincode == $request->pincode) //Pincode
      {
         $influencer->pincode = $request->pincode;
     }
     else {
         $log=new InfluencerLog;
         $log->iid=$user->id;
         $log->des='Pincode changed from '.$influencer->pincode.' to '.$request->pincode;
         $log->save();
         $influencer->pincode = $request->pincode;
     }
        

      

        $insta=InstagramSocial::where('iid',$user->id)->first();
 
         $insta->istatus=$request->istatus;
    
         
         $insta->iqs=$request->iqs;
         $insta->ipfr= $request->ipfr;
        
     

        if($insta->iusernamesstatus == 2 )   //username
        {
                if($insta->iusername == $request->iusername)
                {    
                    $insta->iusernamesstatus=2;
                }
                else{
                    $log=new SocialLog;
                    $log->iid=$user->id;
                    $log->des='Instagram Username changed from '.$insta->iusername.' to '.$request->iusername;
                    $log->save();
                    $insta->iusername = $request->iusername;
                    $insta->iusername = $request->iusername;
                    $insta->iusernamesstatus = 0;
                }
         }
         elseif($insta->iusernamesstatus == 1)
         {
                if($insta->iusername == $request->iusername)
                {    
                    $insta->iusernamesstatus=1;
                }
                else {
                    $log=new SocialLog;
                    $log->iid=$user->id;
                    $log->des='Instagram Username changed from '.$insta->iusername.' to '.$request->iusername;
                    $log->save();
                    $insta->iusername = $request->iusername;
                    $insta->iusernamesstatus = 0;
                }
        }
        else{
              if ($insta->iusername == $request->iusername) {
                $insta->iusername = $request->iusername;
                $insta->iusernamesstatus = $request->iusernamesstatus;
              }
              else {  
                $log=new SocialLog;
                $log->iid=$user->id;
                $log->des='Instagram Username changed from '.$insta->iusername.' to '.$request->iusername;
                $log->save();
                $insta->iusername = $request->iusername;
                
                 
            }

            }

            if($insta->ifollowersstatus == 2)   //followers
            {
                    if($insta->ifollowers == $request->ifollowers)
                    {     
                        $insta->ifollowersstatus=2;
                    }
                    else{
                        $log=new SocialLog;
                        $log->iid=$user->id;
                        $log->des='Instagram Followers changed from '.$insta->ifollowers.' to '.$request->ifollowers;
                        $log->save();
                        $insta->ifollowers = $request->ifollowers;
                        $insta->ifollowersstatus = 0;
                    }
             }
             elseif($insta->ifollowersstatus == 1)
             {
                    if($insta->ifollowers == $request->ifollowers)
                    {     
                        $insta->ifollowersstatus=1;
                    }
                    else {
                        $log=new SocialLog;
                        $log->iid=$user->id;
                        $log->des='Instagram Username changed from '.$insta->ifollowers.' to '.$request->ifollowers;
                        $log->save();
                        $insta->ifollowers = $request->ifollowers;
                        $insta->ifollowersstatus = 0;
                    }
            }
            else{
                if ($insta->ifollowers == $request->ifollowers) {
                    $insta->ifollowers = $request->ifollowers;
                    $insta->ifollowersstatus = $request->ifollowersstatus;
                  }
                  else {  
                    $log=new SocialLog;
                    $log->iid=$user->id;
                    $log->des='Instagram Followers changed from '.$insta->ifollowers.' to '.$request->ifollowers;
                    $log->save();
                    $insta->ifollowers=  $request->ifollowers;
                     
                }
                   
            }

                if($insta->ipostsstatus == 2)   //post
                {
                        if($insta->iposts == $request->iposts)
                        {     
                            $insta->ipostsstatus=2;
                        }
                        else{
                            $log=new SocialLog;
                            $log->iid=$user->id;
                            $log->des='Instagram Posts changed from '.$insta->iposts.' to '.$request->iposts;
                            $log->save();
                            $insta->iposts = $request->iposts;
                            $insta->ipostsstatus = 0;
                        }
                 }
                 elseif($insta->ipostsstatus == 1)
                 {
                        if($insta->iposts == $request->iposts)
                        {     
                            $insta->ipostsstatus=1;
                        }
                        else {
                            $log=new SocialLog;
                            $log->iid=$user->id;
                            $log->des='Instagram Posts changed from '.$insta->iposts.' to '.$request->iposts;
                            $log->save();
                            $insta->iposts = $request->iposts;
                            $insta->ipostsstatus = 0;
                        }
                }
                else{
                   
                    if ($insta->iposts == $request->iposts) {
                        $insta->iposts = $request->iposts;
                        $insta->ipostsstatus = $request->ipostsstatus;
                      }
                      else {  
                        $log=new SocialLog;
                        $log->iid=$user->id;
                        $log->des='Instagram Posts changed from '.$insta->iposts.' to '.$request->iposts;
                        $log->save();
                        $insta->iposts=  $request->iposts;
                         
                    }
                }

            if($insta->iavglikestatus == 2)   //avglikes
            {
                    if($insta->iavglike == $request->iavglike)
                    {     
                        $insta->iavglikestatus=2;
                    }
                    else{
                        $insta->iavglike = $request->iavglike;
                        $insta->iavglikestatus = 0;
                    }
                }
                elseif($insta->iavglikestatus == 1)
                {
                    if($insta->iavglike == $request->iavglike)
                    {     
                        $insta->iavglikestatus=1;
                    }
                    else {
                        $log=new SocialLog;
                        $log->iid=$user->id;
                        $log->des='Instagram Avg Likes changed from '.$insta->iavglike.' to '.$request->iavglike;
                        $log->save();
                        $insta->iavglike = $request->iavglike;
                        $insta->iavglikestatus = 0;
                    }
            }
            else{
                if ($insta->iavglike == $request->iavglike) {
                    $insta->iavglike = $request->iavglike;
                    $insta->iavglikestatus = $request->iavglikestatus;
                  }
                  else {  
                    $log=new SocialLog;
                    $log->iid=$user->id;
                    $log->des='Instagram Avg Likes changed from '.$insta->iavglike.' to '.$request->iavglike;
                    $log->save();
                    $insta->iavglike=  $request->iavglike;
                     
                }
                    
                }

            if($insta->iavgcmtstatus == 2)   //avgcmt
            {
                    if($insta->iavgcmt == $request->iavgcmt)
                    {     
                        $insta->iavgcmtstatus=2;
                    }
                    else{
                        $insta->iavgcmt = $request->iavgcmt;
                        $insta->iavgcmtstatus = 0;
                    }
                }
                elseif($insta->iavgcmtstatus == 1)
                {
                    if($insta->iavgcmt == $request->iavgcmt)
                    {     
                        $insta->iavgcmtstatus=1;
                    }
                    else {
                        $insta->iavgcmt = $request->iavgcmt;
                        $insta->iavgcmtstatus = 0;
                    }
            }
            else{
                if ($insta->iavgcmt == $request->iavgcmt) {
                    $insta->iavgcmt = $request->iavgcmt;
                    $insta->iavgcmtstatus = $request->iavgcmtstatus;
                  }
                  else {  
                    $log=new SocialLog;
                    $log->iid=$user->id;
                    $log->des='Instagram Avg Comments changed from '.$insta->iavgcmt.' to '.$request->iavgcmt;
                    $log->save();
                    $insta->iavgcmt=  $request->iavgcmt;
                     
                }       
                }
                $insta->iaudienceloc == $request->iaudienceloc;
                
        // if($insta->iaudlocstatus == 2)   //aloc
        // {
        //         if($insta->iaudienceloc == $request->iaudienceloc)
        //         {     
        //             $insta->iaudlocstatus=2;
        //         }
        //         else{
                   
        //             $insta->iaudienceloc = $request->iaudienceloc;
        //             $insta->iaudlocstatus = 0;
        //         }
        //     }
        //     elseif($insta->iaudlocstatus == 1)
        //     {
        //         if($insta->iaudienceloc == $request->iaudienceloc)
        //         {     
        //             $insta->iaudlocstatus=1;
        //         }
        //         else {
                
        //             $insta->iaudienceloc = $request->iaudienceloc;
        //             $insta->iaudlocstatus = 0;
        //         }
        // }
        // else{
            
        //         $insta->iaudienceloc=  $request->iaudienceloc;
              
        //     }

            if($insta->iaudagestatus == 2)   //aage
            {
                    if($insta->iaudienceage == $request->iaudienceage)
                    {     
                        $insta->iaudagestatus=2;
                    }
                    else{
                        $insta->iaudienceage = $request->iaudienceage;
                        $insta->iaudagestatus = 0;
                    }
                }
                elseif($insta->iaudagestatus == 1)
                {
                    if($insta->iaudienceage == $request->iaudienceage)
                    {     
                        $insta->iaudagestatus=1;
                    }
                    else {
                        $insta->iaudienceage = $request->iaudienceage;
                        $insta->iaudagestatus = 0;
                    }
            }
            else{
                    $insta->iaudienceage=  $request->iaudienceage;
                }
                if($insta->iaudgenestatus == 2)   //agender
                {
                        if($insta->iaudiencegen == $request->iaudiencegen)
                        {   
                              
                            $insta->iaudgenestatus=2;
                        }
                        else{
                            // $log=new SocialLog;
                            // $log->iid=$user->id;
                            // $log->des='Audience Gender changed from '.$insta->iaudiencegen.' to '.$request->iaudiencegen;
                            // $log->save();
                            $insta->iaudiencegen = $request->iaudiencegen;
                            $insta->iaudgenestatus = 0;
                        }
                    }
                    elseif($insta->iaudgenestatus == 1)
                    {
                        if($insta->iaudiencegen == $request->iaudiencegen)
                        {     
                            $insta->iaudgenestatus=1;
                        }
                        else {
                          
                            $insta->iaudiencegen = $request->iaudiencegen;
                            $insta->iaudgenestatus = 0;
                        }
                }
                else{
               
                        $insta->iaudiencegen=  $request->iaudiencegen;
                    }

                if($request->ifollowers <= 10000)
                {
                    $influencer->inftype='Nano';
                }
                elseif ($request->ifollowers > 10000 && $request->ifollowers <= 100000) {
                    $influencer->inftype='Micro';
                }
                elseif ($request->ifollowers > 100000 && $request->ifollowers <= 1000000) {
                    $influencer->inftype='Macro';
                }
                else {
                    $influencer->inftype='Mega';
                }

                if($insta->ifollowers != 0)
                {
              $insta->iengagementrate= ( round( (($insta->iavglike +  $insta->iavgcmt ) / $insta->ifollowers),2 ) *100 );

                }
                else {
                    $insta->iengagementrate= 0;
                }

              
               
                $yftsocial=YFTSocials::where('iid',$user->id)->first();

                if($yftsocial->yurlstatus == 2)   //yURL
                {
                        if($yftsocial->yurl == $request->yurl)
                        {     
                            $yftsocial->yurlstatus=2;
                        }
                        else{
                            
                            $yftsocial->yurlstatus = 0;
                        }
                    }
                    elseif($yftsocial->yurlstatus == 1)
                    {
                        if($yftsocial->yurl == $request->yurl)
                        {     
                            $yftsocial->yurlstatus=1;
                        }
                        else {
                            $log=new SocialLog;
                            $log->iid=$user->id;
                            $log->des='Youtube Channel URL changed from '.$yftsocial->yurl.' to '.$request->yurl;
                            $log->save();
                            $yftsocial->yurl = $request->yurl;
                            $yftsocial->yurlstatus = 0;
                        }
                }
                else{  
                        if ($yftsocial->yurl ==  $request->yurl) {
                            $yftsocial->yurlstatus = 0;
                          }
                          else {
                            $yftsocial->yurl = $request->yurl;
                            $log=new SocialLog;
                            $log->iid=$user->id;
                            $log->des='Youtube Channel URL changed from '.$yftsocial->yurl.' to '.$request->yurl;
                            $log->save();
                        
                             
                        }
                    }


                    if($yftsocial->furlstatus == 2)   //fURL
                    {
                            if($yftsocial->furl == $request->furl)
                            {     
                                $yftsocial->furlstatus=2;
                            }
                            else{
                                $log=new SocialLog;
                                $log->iid=$user->id;
                                $log->des='Facebook Profile URL changed from '.$yftsocial->furl.' to '.$request->furl;
                                $log->save();
                                $yftsocial->furl = $request->furl;
                                $yftsocial->furlstatus = 0;
                            }
                        }
                        elseif($yftsocial->furlstatus == 1)
                        {
                            if($yftsocial->furl == $request->furl)
                            {     
                                $yftsocial->furlstatus=1;
                            }
                            else {
                                $log=new SocialLog;
                                $log->iid=$user->id;
                                $log->des='Facebook Profile URL changed from '.$yftsocial->furl.' to '.$request->furl;
                                $log->save();
                                $yftsocial->furl = $request->furl;
                                $yftsocial->furlstatus = 0;
                            }
                    }
                    else{
                            if ($yftsocial->furl ==  $request->furl) {
                                $yftsocial->furlstatus = 0;
                              }
                              else {
                                $yftsocial->furl = $request->furl;
                                $log=new SocialLog;
                                $log->iid=$user->id;
                                $log->des='Facebook Profile changed from '.$yftsocial->furl.' to '.$request->furl;
                                $log->save();
      
                                 
                            }
                        }

                        if($yftsocial->tusernamestatus == 2)   //TwitterUSer
                        {
                                if($yftsocial->tusername == $request->tusername)
                                {     
                                    $yftsocial->tusernamestatus=2;
                                }
                                else{
                                 
                                    $yftsocial->tusername = $request->tusername;
                                    $yftsocial->tusernamestatus = 0;
                                }
                            }
                            elseif($yftsocial->tusernamestatus == 1)
                            {
                                if($yftsocial->tusername == $request->tusername)
                                {     
                                    $yftsocial->tusernamestatus=1;
                                }
                                else {
                                    $log=new SocialLog;
                                    $log->iid=$user->id;
                                    $log->des='Twitter Username changed from '.$yftsocial->tusername.' to '.$request->tusername;
                                    $log->save();
                                    $yftsocial->tusername = $request->tusername;
                                    $yftsocial->tusernamestatus = 0;
                                }
                        }
                        else{
                       
                            if ($yftsocial->tusername ==  $request->tusername) {
                                $yftsocial->tusernamestatus = 0;
                              }
                              else {
                                $yftsocial->tusername = $request->tusername;
                                $log=new SocialLog;
                                $log->iid=$user->id;
                                $log->des='Twitter Username changed from '.$yftsocial->tusername.' to '.$request->tusername;
                                $log->save();
                               
                                 
                            }
                          }
            

                          if ($request->ifollowers == 0) {
                            $insta->ipfr = 0;
                          }
                          else{
                            $insta->ipfr = round($request->iposts / $request->ifollowers,2);
                          }
         



        if ( $insta->iusernamesstatus == 2 && $insta->ifollowersstatus ==  2 &&
        $insta->ipostsstatus ==  2 && $insta->iavglikestatus ==  2 && 
        $insta->iavgcmtstatus ==  2 && $insta->iaudlocstatus ==  2 && $influencer->categorystatus = 1
        && $insta->iaudagestatus ==  2 && $insta->iaudgenestatus ==  2
        && $yftsocial->yurlstatus ==  2 && $yftsocial->furlstatus ==  2
        && $yftsocial->tusernamestatus ==  2) {
            $user->verified = 1;
        }
        else {
            $user->verified = 0;
        }
        
                          
                
         $user->save();
        $yftsocial->save();
        $insta->save();
        $influencer->save();
  

        $request->session()->flash('status', 'Saved Changes');
        return redirect()->back();



    
    }
    public function influencersimport(Request $request)
    {
    // return $request;
    
    $file = $_FILES["file"]["tmp_name"];
    $file_open = fopen($file,"r");
    while(($csv = fgetcsv($file_open, 1000, ",")) !== false)
    {

        // return $csv;
    
                $user= new User;
                $user->id=$csv[0];
                $user->name=$csv[1];
                $user->email=$csv[2];
                $user->password=Hash::make("YOLOUD001");
                $user->type=0;
                $user->accountstatus=0;
                $user->profilestatus=0;

                $inf=new Influencer;
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
                $inf->payment=json_encode($x);
                $inf->occupation=json_encode($p);
                $inf->iid = $csv[0];
                $inf->phone = $csv[3];
                $inf->dob = date('Y-m-d',strtotime($csv[4]));
                $inf->gender = $csv[5];
                $inf->city = $csv[6];
                $inf->state = $csv[7];
                $inf->pincode = $csv[8];

                $insta=new InstagramSocial;
                $insta->iid = $csv[0];

                $yft=new YFTSocials;
                $yft->iid = $csv[0];
              
                 
                $yft->save();
                $insta->save();
                $user->save();
                $inf->save();
       
    }
 //return $request;
 $request->session()->flash('status', 'Imported successfully');

     return redirect('/admin/influencer');

    }

    public function listBrand(Request $request)
    {
         $brand = Brand::select('companyname','designation','offering','city','phonenumber','offering','id','created_at','name','email')->with('campaigncreated');
      
        if($request->name){
            $brand->where('name','LIKE','%'.$request->name.'%');
        }
        if($request->id){
            $brand->where('id','LIKE','%'.$request->id.'%');
        }
        if($request->email){
            $brand->where('email','LIKE','%'.$request->email.'%');
        }
        $brand=$brand->paginate(20);

        // return $brand;
        return view('Admin/Brand/list',['brand'=>$brand]);
    }
    public function addBrand(){
       
        return view('Admin/Brand/add');
    }
    public function storeBrand(Request $request){
        //  return $request;
        $validate = Validator::make($request->all(),[
            'name' => 'required|max:255',
            'email' => 'required',
            'phonenumber' => 'required',
            'companyname' => 'required',
            'designation' => 'required',
            'pincode' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'offering' => 'required',
            'brandphoto' =>'nullable|image|mimes:jpeg,png,jpg,svg|max:7168',
      
           
        ]);
        if ($validate->fails()) {
            return back()->withInput()->withErrors($validate);
        }
       
        $brand=new Brand;
        $brand->name=$request->name;
        $brand->email=$request->email;
        $brand->companyname=$request->companyname;
        $brand->designation=$request->designation;
        $brand->city=$request->city;
        $brand->state=$request->state;
        $brand->country=$request->country;
        $brand->pincode=$request->pincode;
        $brand->phonenumber=$request->phonenumber;
        $brand->offering=$request->offering;

   

     
            while(1){
                $strrand=Str::random(10);
                if(Brand::where('brandphoto','/storage/brand/'.$strrand)->count()==0){
                    break;
                }
            }
                $image = $request->file('brandphoto');
                File::delete($brand->brandphoto);
                $newfilename = $strrand. "." . $request->file('brandphoto')->getClientOriginalExtension();
                Storage::disk('public')->put('brand/' . $newfilename, File::get($image));
                $brand->brandphoto='/storage/brand/'.$newfilename;
        


        $brand->brandphoto='/storage/brand/'.$newfilename;

        $brand->save();
       
        $request->session()->flash('status', 'Brand Created Succesfully');
        return redirect('/admin/brand');

    }
    
    public function editBrand($id)
    {

        $brand = Brand::find($id);
        $campaign = Campaign::where('iid',$id)->get();

        
        return view('Admin/Brand/edit',['brand'=>$brand,'campaign'=>$campaign]);
 
    }
    public function updateBrand(Request $request,$id)
    {
        //   return $request;
           
        $validate = Validator::make($request->all(),[
            'name' => 'required|max:255',
            'email' => 'required',
            'phonenumber' => 'required',
            'companyname' => 'required',
            'designation' => 'required',
            'pincode' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'offering' => 'required',
            // 'brandphoto' =>'nullable|image|mimes:jpeg,png,jpg,svg|max:7168',
      
           
        ]);
        if ($validate->fails()) {
            return back()->withInput()->withErrors($validate);
        }

         $brand= Brand::find($id);
         $brand->name=$request->name;
         $brand->email=$request->email;
        $brand->companyname=$request->companyname;
        $brand->designation=$request->designation;
        $brand->city=$request->city;
        $brand->state=$request->state;
        $brand->country=$request->country;
        $brand->phonenumber=$request->phonenumber;
        $brand->offering=$request->offering;
         $brand->phonestatus=$request->phonestatus;
         $brand->emailstatus=$request->emailstatus;
        $brand->save();

        return redirect('admin/brand');
     

    }
    public function validatebrand(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'name' => 'required|max:100',
            'email' => 'required|email',
            'phonenumber' => 'required',
            'companyname' => 'required',
            'designation' => 'required',
            'pincode' => 'required',
            'offering' => 'required',
      
           
        ],

        [
            'name.max' => 'Name should not be more than 100 characters.',
            'name.required' => 'Name is a required field.',
            'email.required' => 'Email is a required field.',
            'email.email' => 'Enter the valid email.',
            'phonenumber.required' => 'Phone Number is a required field.',
            'companyname.required' => 'Company Name is a required field.',
            'designation.required' => 'Designation is a required field.',
            'pincode.required' => 'Pincode is a required field.',
            'offering.required' => 'Offering is a required field.',
        ]
    );
        if ($validate->fails()) {
            return response()->json(['isvalid'=>false,'data'=>$validate->errors()]);
        }
        else{
            return response()->json(['isvalid'=>true]);
        }
    }

    public function brandimport(Request $request)
    {
    // return $request;
    $file=$request->file('file');
        Excel::import(new BrandImport, $file);


     return redirect('/admin/brand/');

    }




}
