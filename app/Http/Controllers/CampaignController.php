<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Brand;
use App\Influencer;
use App\Intrest;
use App\InstagramSocial;
use App\CampaignInflList;
use App\Campaign;
use Validator;
use Auth;
use Str;
use File;
use Storage;
use App\Mail\CampaignMarkedAsNotSuitable;
use App\Mail\CampaignDeliverablesRejected;
use App\Mail\CampaignDeliverablesAccepted;
use App\Mail\NewCampaignOpportunityReceived;

use Illuminate\Support\Facades\Notification;
use App\Mail\NewCreator;
use App\Mail\CampaignOpportunityReceived;
use App\User;
use Illuminate\Support\Facades\Mail;
class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $data = Campaign::with('brandlinked');
        if($request->name){
            $brand=Brand::where('name','LIKE','%'.$request->name.'%')->pluck('id');
            $data=$data->whereIn('id',$brand);
        }
        if($request->campaigntitle){
            $data->where('campaigntitle','LIKE','%'.$request->campaigntitle.'%');
        }
        if($request->id){
            $data->where('id','LIKE','%'.$request->id.'%');
        }
    
        $data = $data->paginate(20);
        $ongoing = Campaign::where('startdate','<',date('y-m-d'))->where('enddate','>',date('y-m-d'))->get();
        $completed = Campaign::where('enddate','<',date('y-m-d'))->get();
        $shed = Campaign::where('startdate','>',date('y-m-d'))->get();
     
         return view('Admin/Campaign/list',['data'=>$data,'ongoing'=>$ongoing,'completed'=>$completed,'shed'=>$shed]);
    }

    public function createcampaign()
    {

          $data=Brand::select('id','companyname')->get();
          $intrest=Intrest::select('name')->get();
        return view('Admin/Campaign/create',['data'=>$data,'intrest'=>$intrest]);
    }

    public function storecampaign(Request $request)
    {
        
        //   return $request;
        $validate = Validator::make($request->all(),[
            'campaigntitle' => 'required|max:50',
            'id'=>'required',
       
            'campaignobj' => 'required',
            'campaigndes' => 'required|max:1500',
            'startdate' => 'required',
            'enddate' => 'required',
            'cexpense' => 'required',
            'otherinfo' => 'max:1500',
      
           
        ],

        [
           
            'id.required' => 'ID is a required field.',
            'campaigntitle.required' => 'Campaign Title is a required field.',
            'campaigntitle.max' => 'Campaign Title should not be more than 50 characters.',
            'campaignobj.required' => 'Campaign Objective is a required field.',
            'campaigndes.required' => 'Campaign Description is a required field.',
            'campaigndes.max' => 'Campaign Description should not be more than 1500 characters.',
            'startdate.required' => 'Application Start Date is a required field.',
            'enddate.required' => 'Application End Date is a required field.',
            'cexpense.required' => 'This is a required question.',
            'otherinfo.max' => 'The information should not be more than 1500 characters.',
 
        ]
    );
        // return $validate->errors();
        if ($validate->fails()) {
            return back()->withInput()->withErrors($validate);
        }
        $data=new Campaign;
        $data->iid=$request->id;
        $data->campaigntitle=$request->campaigntitle;
        $data->campaignobj=$request->campaignobj;
        $data->campaigndes=$request->campaigndes;
        $data->startdate=$request->startdate;
        $data->enddate=$request->enddate;

       
        
        $data->picked1=$request->picked1;
        $data->scategory=$request->scategory;
        $data->picked2=$request->picked2;
        $data->stype=$request->stype;
        $data->picked3=$request->picked3;
        $data->sage=$request->sage;
        $data->picked4=$request->picked4;

        $data->smgender=$request->smgender;
        $data->sfgender=$request->sfgender;
        $data->picked5=$request->picked5;
        $data->slocation=$request->slocation;

        $data->picked6=$request->picked6;
        $data->saage=$request->saage;
        $data->picked7=$request->picked7;
        $data->samgender=$request->samgender;
        $data->safgender=$request->safgender;
        $data->picked8=$request->picked8;
        $data->salocation=$request->salocation;

        $data->pproduct=$request->pproduct;
        $data->orderedby=$request->orderedby;
        $data->pprice=$request->pprice;
        $data->crange=$request->crange;
        $data->cexpense=$request->cexpense;
        $data->otherinfo=$request->otherinfo;
        $data->save();


      

       
        return redirect('/admin/campaign');
    }
    public function editcampaign($id)
    {
      
        $data=Campaign::find($id);
        $brand=Brand::find($data->iid);
        return view('Admin/Campaign/edit',['data'=>$data,'brand'=>$brand]);
    }
    public function updatecampaign($id,Request $request)
    {
        // return $request ;
        $data=Campaign::find($id);
        $data->campaigntitle=$request->campaigntitle;
        $data->campaignobj=$request->campaignobj;
        $data->campaigndes=$request->campaigndes;
        $data->startdate=$request->startdate;
        $data->enddate=$request->enddate;
        
     
        $data->picked1=$request->picked1;
        $data->scategory=$request->scategory;
        $data->picked2=$request->picked2;
        $data->stype=$request->stype;
        $data->picked3=$request->picked3;
        $data->sage=$request->sage;
        $data->picked4=$request->picked4;
        $data->smgender=$request->smgender;
        $data->sfgender=$request->sfgender;
        $data->picked5=$request->picked5;
        $data->slocation=$request->slocation;

        $data->picked6=$request->picked6;
        $data->saage=$request->saage;
        $data->picked7=$request->picked7;
        $data->samgender=$request->samgender;
        $data->safgender=$request->safgender;
        $data->picked8=$request->picked8;
        $data->salocation=$request->salocation;

        $data->pproduct=$request->pproduct;
        $data->orderedby=$request->orderedby;
        $data->pprice=$request->pprice;
        $data->crange=$request->crange;
        $data->cexpense=$request->cexpense;
        $data->otherinfo=$request->otherinfo;
        $data->save();

        return redirect('/admin/campaign');
        
    }
    public function newcreator($id)
    {
        $data = Campaign::find($id);


         $influencer = Influencer::join('users', 'users.id', '=', 'influencers.iid')
         ->join('instagram_socials', 'instagram_socials.iid', '=', 'influencers.iid');
         // return $data->get();
         if($data->scategory){
             $tmp= explode(",",$data->scategory);
             for($i=0;$i<count($tmp);$i++){
                 $influencer=$influencer->orWhere('intrest','LIKE','%'.$tmp[$i].'%');
             }
         }
         if($data->stype){
             $tmp= explode(",",$data->stype);
             for($i=0;$i<count($tmp);$i++){
                 $influencer=$influencer->orWhere('inftype','LIKE','%'.$tmp[$i].'%');
             }
         }
         if($data->sage){
             $tmp= explode(",",$data->sage);
             for($i=0;$i<count($tmp);$i++){
                $influencer=$influencer->orWhere('instagram_socials.iaudienceage','LIKE','%'.$tmp[$i].'%');
             }
         }
         if($data->smgender){
            $tmp= explode(",",$data->smgender);
            for($i=0;$i<count($tmp);$i++){
                $influencer=$influencer->orWhere('gender',$tmp[$i]);
            }
        }
        if($data->sfgender){
            $tmp= explode(",",$data->sfgender);
            for($i=0;$i<count($tmp);$i++){
                $influencer=$influencer->orWhere('gender',$tmp[$i]);
            }
        }
         if($data->slocation){
             $tmp= explode(",",$data->slocation);
             for($i=0;$i<count($tmp);$i++){
                 $influencer=$influencer->orWhere('city','LIKE','%'.$tmp[$i].'%');
             }
         }
         if($data->saage){
             $tmp= explode(",",$data->saage);
             for($i=0;$i<count($tmp);$i++){
                 $influencer=$influencer->orWhere('instagram_socials.iaudienceage','LIKE','%'.$tmp[$i].'%');
             
             }
         }
         if($data->samgender){
            $tmp= explode(",",$data->samgender);
            for($i=0;$i<count($tmp);$i++){
                $influencer=$influencer->orWhere('instagram_socials.iaudiencegen',$tmp[$i]);
            }
        }
        if($data->safgender){
            $tmp= explode(",",$data->safgender);
            for($i=0;$i<count($tmp);$i++){
                $influencer=$influencer->orWhere('instagram_socials.iaudiencegen',$tmp[$i]);
            }
        }
         if($data->salocation){
             $tmp= explode(",",$data->salocation);
             for($i=0;$i<count($tmp);$i++){
                 $influencer=$influencer->orWhere('instagram_socials.iaudienceloc','LIKE','%'.$tmp[$i].'%');
             }
         }
 

    $influencer = $influencer->get()->unique('iid')->pluck('iid');
     $list = CampaignInflList::where('cid',$id)->get()->unique('iid')->pluck('iid');
 
   
         $fin = Influencer::whereNotIn('iid',$influencer)->whereNotIn('iid',$list)->get()->unique('iid')->pluck('iid');
     

      return view('Admin/Campaign/newcreator',['data'=>$data,'fin'=>$fin]);
    }


    public function addnewcreator($id,Request $request)
    {
       
        
        $campaign = Campaign::find($id);
      
        $data = User::find($request->id);
        
        Mail::to($data->email)->send(new NewCampaignOpportunityReceived($data,$campaign));
        $request->session()->flash('status', 'New Created added successfully');
        return redirect()->back();
        
    }



    public function exportinsights($id,Request $request)
    {
    
        $camp = CampaignInflList::with('suser')->with('instagramuser')
        ->with('influencer')
        ->where('cid',$id)
        ->where('status','>',5)
        ->get();
    
   
  
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="Campaign_Insights.csv"');
            $fp = fopen('php://output', 'wb');
            
            $x=['UID','Name','Email','DOB','Category','City','Type','Phone Number','Languages','Audience Age','Audience Gender','Audience Location','Views','Likes','Comments','Save','Share','Reach','Impressions','CTR','CPM'];
            fputcsv($fp, $x);
            
            for ($i=0;$i<$camp->count() ;$i++) {
                    
                $x=[];    

                array_push($x,$camp[$i]->suser->id,$camp[$i]->suser->name,$camp[$i]->suser->email,$camp[$i]->influencer->dob,$camp[$i]->influencer->intrest,$camp[$i]->influencer->city,$camp[$i]->influencer->inftype,$camp[$i]->influencer->phone,$camp[$i]->influencer->languages,$camp[$i]->instagramuser->iaudienceage,$camp[$i]->instagramuser->iaudiencegen,$camp[$i]->instagramuser->iaudienceloc,$camp[$i]->views,$camp[$i]->likes,$camp[$i]->comments,$camp[$i]->save,$camp[$i]->share,$camp[$i]->reach,$camp[$i]->impressions,$camp[$i]->ctr,$camp[$i]->cpm);
                
                fputcsv($fp, $x);
                
            }
            fclose($fp);
     

    }


    public function viewcamp($id,Request $request)
    {
        $data = Campaign::find($id);
        $camplist = CampaignInflList::where('cid',$id)->get();
        $cmatch = $camplist
        ->count('iid');
        $screator = $camplist
        ->where('status','>=',1)
        ->where('status','!=',5);
        $fcreator = $camplist
        ->where('status','>=',2)
        ->where('status','!=',5);
        $mousent = $camplist
        ->where('status','=',6);
        $mouacc = $camplist
        ->where('status','=',7);
        $cds = $camplist
        ->where('status','=',8);
        $cr = $camplist
        ->where('status','=',9);
        $ca = $camplist
        ->where('status','=',10);
        $cp = $camplist
        ->where('status','=',11);
        $pir = $camplist
        ->where('status','=',12);
        $cd = $camplist
        ->where('status','=',13);
      

  
         if($request->type >= 1 && $request->type < 6){

            if($request->type == 1){
             
                    $influencer = Influencer::join('users', 'users.id', '=', 'influencers.iid')
                    ->join('instagram_socials', 'instagram_socials.iid', '=', 'influencers.iid');
                    // return $data->get();
                    if($data->scategory){
                        $tmp= explode(",",$data->scategory);
                        for($i=0;$i<count($tmp);$i++){
                            $influencer=$influencer->orWhere('intrest','LIKE','%'.$tmp[$i].'%');
                        }
                    }
                    if($data->stype){
                        $tmp= explode(",",$data->stype);
                        for($i=0;$i<count($tmp);$i++){
                            $influencer=$influencer->orWhere('inftype','LIKE','%'.$tmp[$i].'%');
                        }
                    }
                    if($data->sage){
                        $tmp= explode(",",$data->sage);
                        for($i=0;$i<count($tmp);$i++){
                            $influencer=$influencer->orWhere('instagram_socials.iaudienceage','LIKE','%'.$tmp[$i].'%');
                        }
                    }
                    if($data->smgender){
                        $tmp= explode(",",$data->smgender);
                        for($i=0;$i<count($tmp);$i++){
                            $influencer=$influencer->orWhere('gender',$tmp[$i]);
                        }
                    }
                    if($data->sfgender){
                        $tmp= explode(",",$data->sfgender);
                        for($i=0;$i<count($tmp);$i++){
                            $influencer=$influencer->orWhere('gender',$tmp[$i]);
                        }
                    }
                    if($data->slocation){
                        $tmp= explode(",",$data->slocation);
                        for($i=0;$i<count($tmp);$i++){
                            $influencer=$influencer->orWhere('city','LIKE','%'.$tmp[$i].'%');
                        }
                    }
                    if($data->saage){
                        $tmp= explode(",",$data->saage);
                        for($i=0;$i<count($tmp);$i++){
                            $influencer=$influencer->orWhere('instagram_socials.iaudienceage','LIKE','%'.$tmp[$i].'%');
                        
                        }
                    }
                    if($data->samgender){
                        $tmp= explode(",",$data->samgender);
                        for($i=0;$i<count($tmp);$i++){
                            $influencer=$influencer->orWhere('instagram_socials.iaudiencegen',$tmp[$i]);
                        }
                    }
                    if($data->safgender){
                        $tmp= explode(",",$data->safgender);
                        for($i=0;$i<count($tmp);$i++){
                            $influencer=$influencer->orWhere('instagram_socials.iaudiencegen',$tmp[$i]);
                        }
                    }
                    if($data->salocation){
                        $tmp= explode(",",$data->salocation);
                        for($i=0;$i<count($tmp);$i++){
                            $influencer=$influencer->orWhere('instagram_socials.iaudienceloc','LIKE','%'.$tmp[$i].'%');
                        }
                    }
            

               $influencer = $influencer->get();
  
            }

            else if($request->type == 2)
            {
                  $data = Campaign::find($id);
                  $influencer = CampaignInflList::with('user')->with('influencers')->with('instagramusers')->where('cid',$data->id)->get();
                  
            }
            else if($request->type == 3)
            {
                  $data = Campaign::find($id);
                  $influencer = CampaignInflList::with('user')->with('influencers')->with('instagramusers')
                  ->where('cid',$id)                  
                  ->get();
                  $screator = $influencer
                  ->where('status','>=',1)
                  ->where('status','!=',5);
                  
            }
            else if($request->type == 4)
            {
                  $data = Campaign::find($id);
                  $influencer = CampaignInflList::with('user')->with('influencers')->with('instagramusers')
                  ->where('cid',$id)
                  ->get();
                  $fcreator = $influencer
                ->where('status','>=',2)
                ->where('status','!=',3)
                ->where('status','!=',4)
                ->where('status','!=',5);
                  
            }
            else if($request->type == 5)
            {
                  $data = Campaign::find($id);
                  $influencer = CampaignInflList::with('user')->with('influencers')->with('instagramusers')
                  ->where('cid',$id)
                  ->get();
                  $fcreator = $influencer
                  ->where('status','>=',2)
                  ->where('status','!=',3)
                  ->where('status','!=',4)
                  ->where('status','!=',5);
                  
            }

         

            return view('Admin/Campaign/view',['influencer'=>$influencer,'data'=>$data,'camplist'=>$camplist,
            'screator'=>$screator,'fcreator'=>$fcreator,'mousent'=>$mousent,'mouacc'=>$mouacc,'cds'=>$cds,'cr'=>$cr,
            'ca'=>$ca,'cp'=>$cp,'pir'=>$pir,'cd'=>$cd,'cmatch'=>$cmatch]);           
          
        }
        
        return redirect('/admin/campaign/view/'.($data->id).'?type=1');
        
    }
    public function validatecamp(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'campaigntitle' => 'required|max:50',
            'id'=>'required',
          
            'campaignobj' => 'required',
            'campaigndes' => 'required|max:1500',
            'startdate' => 'required',
            'enddate' => 'required',
            'cexpense' => 'required',
            'otherinfo' => 'max:1500',
      
           
        ],

        [
        
            'id.required' => 'ID is a required field.',
            'campaigntitle.required' => 'Campaign Title is a required field.',
            'campaigntitle.max' => 'Campaign Title should not be more than 50 characters.',
            'campaignobj.required' => 'Campaign Objective is a required field.',
            'campaigndes.required' => 'Campaign Description is a required field.',
            'campaigndes.max' => 'Campaign Description should not be more than 1500 characters.',
            'startdate.required' => 'Application Start Date is a required field.',
            'enddate.required' => 'Application End Date is a required field.',
            'cexpense.required' => 'This is a required question.',
            'otherinfo.max' => 'The information should not be more than 1500 characters.',
 
        ]
    );
        if ($validate->fails()) {
            return response()->json(['isvalid'=>false,'data'=>$validate->errors()]);
        }
        else{
            return response()->json(['isvalid'=>true]);
        }
    

    }


//USER's Campaign STARTS --

    public function userindex(Request $request)
    {
        $user = Auth::user();
        
        
       $brand = Campaign::with('brandlinked')->get();

       $campaign=Campaign::with('influencerlinked')->with('brandlinked');
      
    
      if($user->influencer->intrest != null)
      {
        $lx=explode(",",json_decode($user->influencer->intrest));
        for($i=0;$i<count($lx);$i++){
            $campaign=$campaign->orWhere('scategory','LIKE','%'.$lx[$i].'%');
        }
      }
      
      if($user->influencer->inftype)
      {
        $lx=explode(",",$user->influencer->inftype);
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
        $lx=explode(",",$user->influencer->city);
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
        $lx=explode(",",$user->instagram->iaudienceage);
        for($i=0;$i<count($lx);$i++){
            $campaign=$campaign->orWhere('sage','LIKE','%'.$lx[$i].'%');
        }
      }
      if($user->instagram->iaudienceloc)
      {
        $lx=explode(",",$user->instagram->iaudienceloc);
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
     
        
     
      
 
      if($request->cname){
        $campaign= $campaign->where('campaigntitle','LIKE','%'.$request->cname.'%');
  
     }
   

     
    $campaign=$campaign->orderBy('id','desc')->paginate(6);

    // return $campaign;
     


    
       
        $capp = CampaignInflList::where('iid',$user->id)->get();

      
        $cpar = CampaignInflList::where('iid',$user->id)->where('status','>',5)->get();
    

        $ccom = $capp->where('status','=',13);
        
        $cpaarr = CampaignInflList::where('iid',$user->id)->where('status','=',2)->get();


        
        
        return view('Influencer/campaign',['campaign'=>$campaign,'capp'=>$capp,
        'cpar'=>$cpar,'ccom'=>$ccom,'cpaarr'=>$cpaarr,'brand'=>$brand]);
    }

    public function cstatus($id)
    {
        $user = Auth::user();
        $campaign = Campaign::find($id);
     
        $data = CampaignInflList::where('cid',$id)->where('iid',$user->id)->first();
        $brand = Brand::where('id',$campaign->iid)->first();
        $napp = CampaignInflList::where('cid',$id)->get();
      
     
        return view('Influencer/campaignstatus',['data'=>$data,'campaign'=>$campaign,'brand'=>$brand,'napp'=>$napp]);
      
        
    }

    public function useraccept(Request $request,$id)
    {
        $data=CampaignInflList::find($id);
        $campaign = Campaign::where('id',$data->cid)->first();
        $data->status= 3;
        $data->save();
        Mail::to('partnerinfluencer@yoloud.com')->send(new CampaignDeliverablesAccepted($data,$campaign));
        $request->session()->flash('status', 'Saved successfully');
        return 1;
    }
    public function userdecline($id)
    {
     
        $data=CampaignInflList::find($id);
        $campaign = Campaign::where('id',$data->cid)->first();
        $data->status= 4;
        $data->save();
        Mail::to('partnerinfluencer@yoloud.com')->send(new CampaignDeliverablesRejected($data,$campaign));
        $request->session()->flash('status', 'Saved successfully');
        return 1;
    }
    public function notsuits(Request $request,$id)
    {
     
        
        $campaign = Campaign::where('id',$id)->first();
    
        Mail::to('partnerinfluencer@yoloud.com')->send(new CampaignMarkedAsNotSuitable($campaign));
        $request->session()->flash('error', 'Marked as not suitable');
        return redirect()->back();
    }
    public function acceptmou($id)
    {  
      $data=CampaignInflList::find($id);
        $data->status= 7;
        $data->save();
        return 1;
    }
    public function addurl(Request $request,$id)
    {  
          $data=CampaignInflList::find($id);
          $data->posturl=$request->posturl;
        $data->status= 11;
        $data->save();
        return 1;
    }
    public function addposin(Request $request,$id)
    {
        $data=CampaignInflList::find($id);
        $data->views=$request->views;
        $data->likes=$request->likes;
        $data->impressions=$request->impressions;
        $data->comments=$request->comments;
        $data->save=$request->save;
        $data->share=$request->share;
        $data->reach=$request->reach;
        
        if (Auth::user()->instagram->ifollowers == 0) {
            $data->engratew= 0;
        }
        else {
            $data->engratew = ( ($request->views + $request->likes + $request->comments + $request->save + $request->share) / Auth::user()->instagram->ifollowers);
        }
         
        if (Auth::user()->instagram->ifollowers == 0) {
            $data->engratewout= 0;
        }
        else {
            $data->engratewout = ( ($request->likes + $request->comments + $request->save + $request->share) / Auth::user()->instagram->ifollowers);
        }

        if ($request->impressions == 0 ) {
            $data->cpm= 0;
        }
        else {
            $data->cpm= ( ($data->commercial / $request->impressions ) * 1000 );
        }
        
        $data->status = 12;
        $data->save();
        $request->session()->flash('status', 'Uploaded Insights successfully');
        return 1;
    }
    public function uploadins(Request $request,$id){
        // return $request;
        $validator = Validator::make($request->all(),[
            'insphoto' =>'nullable|image|mimes:jpeg,png,jpg,svg|max:7168',
        ]);     
        if ($validator->fails())
        {
            $request->session()->flash('error', 'Sorry Check the instructions before uploading');
            return redirect()->back();
        }

        while(1)
        {
            $name=Str::random(20);
            if(CampaignInflList::where('insphoto',$name)->count()==0){
                break;
            }
        }
        $data=CampaignInflList::find($id);
        $image = $request->file('insphoto');
        $newfilename = $name. "." . $request->file('insphoto')->getClientOriginalExtension();
        Storage::disk('public')->put('uploadinsights/' . $newfilename, File::get($image));
        $data->insphoto = $newfilename;
        $data->save();
        $request->session()->flash('status', 'Insights Photo uploaded successfully, now update insights.');
        return redirect()->back();
    }
    public function downloadins($id)
    {
        $dl = CampaignInflList::find($id);
        return response()->download("storage/uploadinsights/{$dl->insphoto}");
        
        
    }


    public function campaccept(Request $request,$cid,$iid,$bid)
    {  
        
        $data = new CampaignInflList;
        $data->cid = $cid;
        $data->iid = $iid;
        $data->bid = $bid ;
        $data->status = 0 ;
        $data->save();
        $request->session()->flash('status', 'Applied Successfully');
        return redirect()->back();
    }
    public function porder(Request $request,$id)
    {  
        $data=CampaignInflList::find($id);
        $data->orderid=$request->orderid;
        $data->pdeldate=$request->pdeldate;
        $data->status= 14;
        $data->save();
        $request->session()->flash('status', 'Saved Successfully');
        return 1;
    }
    public function preceive(Request $request,$id)
    {  
        $data=CampaignInflList::find($id);
        $data->precdate=$request->precdate;
        $data->status= 15;
        $data->save();
        $request->session()->flash('status', 'Saved Successfully');
        return 1;
    }
    public function triggermail($id){

        $data = Campaign::find($id);
        return view('Admin/Campaign/triggermail',['data'=>$data]);
    }
    public function sendmail($id,Request $request){

        $data = Campaign::find($id);
        $influencer = Influencer::join('users', 'users.id', '=', 'influencers.iid')
            ->join('instagram_socials', 'instagram_socials.iid', '=', 'influencers.iid');
            // return $data->get();
            if($data->scategory){
                $tmp= explode(",",$data->scategory);
                for($i=0;$i<count($tmp);$i++){
                    $influencer=$influencer->orWhere('intrest','LIKE','%'.$tmp[$i].'%');
                }
            }
            if($data->stype){
                $tmp= explode(",",$data->stype);
                for($i=0;$i<count($tmp);$i++){
                    $influencer=$influencer->orWhere('inftype','LIKE','%'.$tmp[$i].'%');
                }
            }
            if($data->sage){
                $tmp= explode(",",$data->sage);
                for($i=0;$i<count($tmp);$i++){
                    $influencer=$influencer->orWhere('dob','LIKE','%'.$tmp[$i].'%');
                }
            }
            if($data->smgender){
                $tmp= explode(",",$data->smgender);
                for($i=0;$i<count($tmp);$i++){
                    $influencer=$influencer->orWhere('gender',$tmp[$i]);
                }
            }
            if($data->sfgender){
                $tmp= explode(",",$data->sfgender);
                for($i=0;$i<count($tmp);$i++){
                    $influencer=$influencer->orWhere('gender',$tmp[$i]);
                }
            }
            if($data->slocation){
                $tmp= explode(",",$data->slocation);
                for($i=0;$i<count($tmp);$i++){
                    $influencer=$influencer->orWhere('city','LIKE','%'.$tmp[$i].'%');
                }
            }
            if($data->saage){
                $tmp= explode(",",$data->saage);
                for($i=0;$i<count($tmp);$i++){
                    $influencer=$influencer->orWhere('instagram_socials.iaudienceage','LIKE','%'.$tmp[$i].'%');
                
                }
            }
            if($data->samgender){
                $tmp= explode(",",$data->samgender);
                for($i=0;$i<count($tmp);$i++){
                    $influencer=$influencer->orWhere('instagram_socials.iaudiencegen',$tmp[$i]);
                }
            }
            if($data->safgender){
                $tmp= explode(",",$data->safgender);
                for($i=0;$i<count($tmp);$i++){
                    $influencer=$influencer->orWhere('instagram_socials.iaudiencegen',$tmp[$i]);
                }
            }
            if($data->salocation){
                $tmp= explode(",",$data->salocation);
                for($i=0;$i<count($tmp);$i++){
                    $influencer=$influencer->orWhere('instagram_socials.iaudienceloc','LIKE','%'.$tmp[$i].'%');
                }
            }


                $influencer = $influencer->get();
            
               for($i=0;$i<$influencer->count();$i++)
               {
                   Mail::to($influencer[$i]->email)->send(new CampaignOpportunityReceived($influencer[$i],$data));
               }
               $request->session()->flash('status', 'Mail sent successfully');
               return redirect('/admin/campaign');
    }
  















  

}
