<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\CampaignInflList;
use App\Influencer;
use App\OtherOpp;
use App\Campaign;
use App\InstagramSocial;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(Auth::user()->type==1){
            return redirect('/admin/');
        }
        
      

        $otheropp = OtherOpp::select('otitle','created_at','odes','ocontactus')->where('oppstatus',1)->get();

        
        $earn = CampaignInflList::where('iid',Auth::user()->id)->get()->sum('commercial');
        $rating = CampaignInflList::where('iid',Auth::user()->id)->get()->avg('ratecreator');
        $campper = CampaignInflList::where('iid',Auth::user()->id)->get()->avg('engratewout');

       

        $brandww = CampaignInflList::where('iid',Auth::user()->id)->where('status','>',5)->get()->groupBy('bid');

        $myper = CampaignInflList::where('iid',Auth::user()->id)->where('status','=',13)->get();



        $user = Auth::user();
        
  
        $campaign=Campaign::select('iid','scategory','campaigntitle','campaignobj','campaigndes','id','created_at','startdate','enddate')->with('influencerlinked')->with('brandlinked');
       
  
           
        if($user->influencer->intrest != null)
        {
          $lx=explode(",",json_decode($user->influencer->intrest));
          for($i=0;$i<count($lx);$i++){
              $campaign=$campaign->orWhere('scategory','LIKE','%'.$lx[$i].'%');
          }
        }
        
        if($user->influencer->inftype)
        {
          $lx=explode(",",json_decode($user->influencer->inftype));
          for($i=0;$i<count($lx);$i++){
              $campaign=$campaign->orWhere('stype','LIKE','%'.$lx[$i].'%');
          }
        }
        if($user->influencer->gender == "Male")
        {
            $campaign=$campaign->orWhere('smgender',$user->influencer->gender);
          
        }
        if($user->influencer->gender == "Female")
        {
           $campaign=$campaign->orWhere('sfgender',$user->influencer->gender);
          
        }
        if($user->influencer->city)
        {
          $lx=explode(",",json_decode($user->influencer->city));
          for($i=0;$i<count($lx);$i++){
              $campaign=$campaign->orWhere('slocation','LIKE','%'.$lx[$i].'%');
          }
        }
        if($user->instagram->iaudienceage)
        {
          $lx=explode(",",json_decode($user->instagram->iaudienceage));
          for($i=0;$i<count($lx);$i++){
              $campaign=$campaign->orWhere('saage','LIKE','%'.$lx[$i].'%');
          }
        }
        if($user->instagram->iaudienceage)
        {
          $lx=explode(",",json_decode($user->instagram->iaudienceage));
          for($i=0;$i<count($lx);$i++){
              $campaign=$campaign->orWhere('sage','LIKE','%'.$lx[$i].'%');
          }
        }
        if($user->instagram->iaudienceloc)
        {
          $lx=explode(",",json_decode($user->instagram->iaudienceloc));
          for($i=0;$i<count($lx);$i++){
              $campaign=$campaign->orWhere('salocation','LIKE','%'.$lx[$i].'%');
          }
        }
        if($user->instagram->iaudiencegen == "Male")
        {
              $campaign=$campaign->orWhere('samgender',$user->instagram->iaudiencegen);   
        }
        if($user->instagram->iaudiencegen == "Female")
        {
              $campaign=$campaign->orWhere('safgender',$user->instagram->iaudiencegen);   
        }
        
        $campaign= $campaign->get();
      
        
    
   
      return view('home',['earn'=>$earn,'rating'=>$rating,'brandww'=>$brandww,'otheropp'=>$otheropp,'myper'=>$myper,'campper'=>$campper,'campaign'=>$campaign]);
    }
}
