<?php

namespace App\Http\Controllers;

use App\User;
use App\Influencer;
use App\InstagramSocial;
use App\YFTSocials;
use Illuminate\Http\Request;
use Storage;
use Validator;
use File;
use App\Exports\InfluencerExport;
use Excel;
use Str;

class InfluencerController extends Controller
{
    public function export(Request $request) 
    {
        $user = User::with('influencers')->with('instagrams')->where('type',0)->get();
    $infl = Influencer::all();
    $insta = InstagramSocial::all();
        //   return $user;

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="Influencers.csv"');
        $fp = fopen('php://output', 'wb');
        
        $x=['UID','Name','Email','DOB','Category','City','Type','Phone Number','Languages','Audience Age','Audience Gender','Audience Location','Created At'];
        fputcsv($fp, $x);
        
        for ($i=0;$i<$user->count()&&$i<$infl->count()&&$insta->count();$i++) {
                
            $x=[];          
            array_push($x,$user[$i]->id,$user[$i]->name,$user[$i]->email,$infl[$i]->dob,$infl[$i]->intrest,$infl[$i]->city,$infl[$i]->inftype,$infl[$i]->phone,$infl[$i]->languages,$insta[$i]->iaudienceage,$insta[$i]->iaudiencegen,$insta[$i]->iaudienceloc,$user[$i]->created_at);
            
            fputcsv($fp, $x);
            
        }
        
        fclose($fp);
       
    }


      
       

        // return Excel::download(new InfluencerExport, 'influencer.xlsx');
    

    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function content(Request $request,$id){
        // return $request;
        $validator = Validator::make($request->all(),[
            'contentphoto' =>'nullable|image|mimes:jpeg,png,jpg,svg|max:7168',
        ]);     
        if ($validator->fails())
        {
            $request->session()->flash('error', 'Sorry Check the instructions before uploading');
            return redirect()->back();
        }

        while(1)
        {
            $name=Str::random(20);
            if(Influencer::where('contentphoto',$name)->count()==0){
                break;
            }
        }
        $influencer=Influencer::find($id);
        $image = $request->file('contentphoto');
        $newfilename = $name. "." . $request->file('contentphoto')->getClientOriginalExtension();
        Storage::disk('public')->put('content/' . $newfilename, File::get($image));
        $influencer->contentphoto = $newfilename;
        $influencer->save();
        $request->session()->flash('status', 'Uploaded Insights successfully');
        return redirect()->back();
    }
    public function profilestatus(Request $request,$id)
    {
        
        $data=User::find($id);
        $data->profilestatus=$request->profilestatus;
        $data->save();
        return 1;
    }
    public function accountstatus(Request $request,$id)
    {
        
        $data=User::find($id);
        $data->accountstatus=$request->accountstatus;
        $data->save();
        return 1;
    }
    public function emailstatus(Request $request,$id)
    {
        
        $data=Influencer::find($id);
        $data->emailstatus=$request->emailstatus;
        $data->save();
        return 1;
    }
    public function phonestatus(Request $request,$id)
    {
        $data=Influencer::find($id);
        $data->phonestatus=$request->phonestatus;
        $data->save();
        return 1;
    }
    public function categorystatus(Request $request,$id)
    {
        $data=Influencer::where('iid',$id)->first();
        $data->categorystatus= $request->categorystatus;
        $data->save();
        return 1;
    }
    public function iusernamesstatus(Request $request,$id)
    {
        $data=InstagramSocial::where('iid',$id)->first();
        $data->iusernamesstatus=$request->iusernamesstatus;
        $data->save();
        return 1;
    }
    public function ifollowersstatus(Request $request,$id)
    {
        $data=InstagramSocial::where('iid',$id)->first();
        $data->ifollowersstatus=$request->ifollowersstatus;
        $data->save();
        return 1;
    }
    public function ipostsstatus(Request $request,$id)
    {
        $data=InstagramSocial::where('iid',$id)->first();
        $data->ipostsstatus=$request->ipostsstatus;
        $data->save();
        return 1;
    }
    public function iavglikestatus(Request $request,$id)
    {
        $data=InstagramSocial::where('iid',$id)->first();
        $data->iavglikestatus=$request->iavglikestatus;
        $data->save();
        return 1;
    }
    public function iavgcmtstatus(Request $request,$id)
    {
        $data=InstagramSocial::where('iid',$id)->first();
        $data->iavgcmtstatus=$request->iavgcmtstatus;
        $data->save();
        return 1;
    }
   
    public function iaudagestatus(Request $request,$id)
    {
        $data=InstagramSocial::where('iid',$id)->first();
        $data->iaudagestatus=$request->iaudagestatus;
        $data->save();
        return 1;
    }
    public function iaudlocstatus(Request $request,$id)
    {
        $data=InstagramSocial::where('iid',$id)->first();
        $data->iaudlocstatus=$request->iaudlocstatus;
        $data->save();
        return 1;
    }
    public function iaudgenestatus(Request $request,$id)
    {
        $data=InstagramSocial::where('iid',$id)->first();
        $data->iaudgenestatus=$request->iaudgenestatus;
        $data->save();
        return 1;
    }
    public function yurl(Request $request,$id)
    {
        $data=YFTSocials::where('iid',$id)->first();
        $data->yurlstatus=$request->yurlstatus;
        $data->save();
        return 1;
    }
    public function furl(Request $request,$id)
    {
        $data=YFTSocials::where('iid',$id)->first();
        $data->furlstatus=$request->furlstatus;
        $data->save();
        return 1;
    }
    public function tusername(Request $request,$id)
    {
        $data=YFTSocials::where('iid',$id)->first();
        $data->tusernamestatus=$request->tusernamestatus;
        $data->save();
        return 1;
    }

    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
  

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Influencer  $influencer
     * @return \Illuminate\Http\Response
     */
    public function show(Influencer $influencer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Influencer  $influencer
     * @return \Illuminate\Http\Response
     */
    public function edit(Influencer $influencer)
    {
        //
    }

    public function update(Request $request, Influencer $influencer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Influencer  $influencer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Influencer $influencer)
    {
        //
    }


}
