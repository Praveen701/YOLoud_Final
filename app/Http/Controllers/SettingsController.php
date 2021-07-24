<?php

namespace App\Http\Controllers;

use App\Influencer;
use App\Language;
use App\InstagramSocial;
use App\YFTSocials;
use App\SocialLog;
use App\InfluencerLog;

use App\Intrest;
use Str;
use File;
use Storage;
use Illuminate\Http\Request;
use Auth;
use App\User;
use Validator;
class SettingsController extends Controller
{

    public function index()
    {
        $intrest=Intrest::select('name','id')->get();
        $language=Language::select('name','id')->get();
        $intrests=Influencer::where('iid',Auth::user()->id)->first();
        $insta=InstagramSocial::where('iid',Auth::user()->id)->first();
        $yftsocial=YFTSocials::where('iid',Auth::user()->id)->first();

         return view('Influencer/settings',['intrest'=>$intrest,'influencer'=>$intrests,'insta'=>$insta,'language'=>$language,'yftsocial'=>$yftsocial]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function inpic(Request $request)
    {
        $influencer=Influencer::find(Auth::user()->id);
        
    }
    public function store(Request $request)
    {
        //  return $request;
        $user=User::find(Auth::user()->id);
         
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
        // $user->status=3;
        $user->save();
        $influencer=Influencer::where('iid',$user->id)->first();
    
        
        if ($influencer->phone == $request->phone) //phone
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
          if ( $influencer->city == null) {
            $influencer->city = $request->city;
          }
          else {
            $log=new InfluencerLog;
            $log->iid=$user->id;
            $log->des='City changed from '.$influencer->city.' to '.$request->city;
            $log->save();
            $influencer->city = $request->city;
          }
         
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

        $influencer->gender=$request->gender;
        $influencer->languages=$request->languages?json_encode($request->languages):null;
        $influencer->intrest=$request->intrest?json_encode($request->intrest):null;
       
       
        $influencer->country=$request->country;
        $x=[];
        $x['type']=$request->paytype;
        $x['value']=$request->payval;
        $influencer->payment=json_encode($x);

        $y=[];
        $y['type']=$request->occtype;
        $y['value']=$request->occval;
        $influencer->occupation=json_encode($y);
        

        $insta=InstagramSocial::where('iid',$user->id)->first();
     

        if($insta->iusernamesstatus == 2 )   //username
        {
                if($insta->iusername == $request->iusername)
                {    
                    $insta->iusernamesstatus=2;
                }
                else{
                    $log=new SocialLog;
                    $log->iid=Auth::user()->id;
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
                    $log->iid=Auth::user()->id;
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
                $log->iid=Auth::user()->id;
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
                        $log->iid=Auth::user()->id;
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
                        $log->iid=Auth::user()->id;
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
                    $log->iid=Auth::user()->id;
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
                            $log->iid=Auth::user()->id;
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
                            $log->iid=Auth::user()->id;
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
                            $log->iid=Auth::user()->id;
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
                        $log->iid=Auth::user()->id;
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
                    $log->iid=Auth::user()->id;
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
                    $log->iid=Auth::user()->id;
                    $log->des='Instagram Avg Comments changed from '.$insta->iavgcmt.' to '.$request->iavgcmt;
                    $log->save();
                    $insta->iavgcmt=  $request->iavgcmt;
                     
                }       
                }

        if($insta->iaudlocstatus == 2)   //aloc
        {
                if($insta->iaudienceloc == $request->iaudienceloc)
                {     
                    $insta->iaudlocstatus=2;
                }
                else{
                   
                    $insta->iaudienceloc = $request->iaudienceloc;
                    $insta->iaudlocstatus = 0;
                }
            }
            elseif($insta->iaudlocstatus == 1)
            {
                if($insta->iaudienceloc == $request->iaudienceloc)
                {     
                    $insta->iaudlocstatus=1;
                }
                else {
                
                    $insta->iaudienceloc = $request->iaudienceloc;
                    $insta->iaudlocstatus = 0;
                }
        }
        else{
            
                $insta->iaudienceloc=  $request->iaudienceloc;
              
            }

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
                    $log->iid=Auth::user()->id;
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
                    $log->iid=Auth::user()->id;
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
                        $log->iid=Auth::user()->id;
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
                        $log->iid=Auth::user()->id;
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
                        $log->iid=Auth::user()->id;
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
                            $log->iid=Auth::user()->id;
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
                        $log->iid=Auth::user()->id;
                        $log->des='Twitter Username changed from '.$yftsocial->tusername.' to '.$request->tusername;
                        $log->save();
                       
                         
                    }
                  }





        $yftsocial->save();
        $influencer->save();
        $insta->save();

       

        $request->session()->flash('status', 'Thank You for updating your profile details. Your profile is under review.');
        return redirect('/settings');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function changeavatar(Request $request){
        // return $request;
        $validator = Validator::make($request->all(),[
            'photo' =>'nullable|image|mimes:jpeg,png,jpg,svg|max:7168',
        ]);
        if ($validator->fails())
        {
            $request->session()->flash('error', 'Sorry Check the instructions before uploading');
            return redirect('/home');
        }

        while(1)
        {
            $name=Str::random(20);
            if(User::where('avatar',$name)->count()==0){
                break;
            }
        }
        $user=User::find(Auth::user()->id);
        $image = $request->file('photo');
        $newfilename = $name. "." . $request->file('photo')->getClientOriginalExtension();
        Storage::disk('public')->put('profile/' . $newfilename, File::get($image));
        $user->avatar = $newfilename;
        $user->save();
        $request->session()->flash('status', 'Changed avatar successfully');
        return redirect('/home');
    }

    public function uploadins(Request $request){
        // return $request;
        $validator = Validator::make($request->all(),[
            'contentphoto' =>'nullable|image|mimes:jpeg,png,jpg,svg|max:7168',
        ]);     
        if ($validator->fails())
        {
            $request->session()->flash('error', 'Sorry Check the instructions before uploading');
            return redirect('/settings');
        }

        while(1)
        {
            $name=Str::random(20);
            if(Influencer::where('contentphoto',$name)->count()==0){
                break;
            }
        }
        $influencer=Influencer::where('iid',Auth::user()->id)->first();
        $image = $request->file('contentphoto');
        $newfilename = $name. "." . $request->file('contentphoto')->getClientOriginalExtension();
        Storage::disk('public')->put('content/' . $newfilename, File::get($image));
        $influencer->contentphoto = $newfilename;
        $influencer->save();
        $request->session()->flash('status', 'Updated Audience Insights successfully');
        return redirect('/settings');
    }

}
