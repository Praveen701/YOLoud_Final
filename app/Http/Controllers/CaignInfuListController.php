<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CampaignInflList;
use App\Campaign;
use App\Influencer;
use Auth;
use App\Mail\ApplicationFinalized;
use App\Mail\ApplicationShortlisted;
use App\Mail\ApplicationDeclined;
use App\Mail\ApplicationRejected;
use App\Mail\NewOfferReceived;
use App\Mail\CommercialsReceived;


use App\InstagramSocial;

use App\Exports\InsightsExport;
use Excel;

use Illuminate\Support\Facades\Mail;

class CaignInfuListController extends Controller
{
    public function export(Request $request) 
    {
        return Excel::download(new InsightsExport, 'insights.xlsx');
    }
    public function admindecline(Request $request,$id)
    {
        $data=CampaignInflList::find($id);
        $user=User::where('id',$data->iid)->first();
        $campaign=Campaign::where('id',$data->cid)->first();
        $data->status= 5;
        $data->save();
        Mail::to($user->email)->send(new ApplicationDeclined($data,$campaign));
        return 1;
    }


    public function finalstatus(Request $request,$id)
    {
        $data=CampaignInflList::find($id);
        $user=User::where('id',$data->iid)->first();
        $campaign=Campaign::where('id',$data->cid)->first();
        $data->status=$request->status;
        $data->save();
         Mail::to($user->email)->send(new ApplicationFinalized($data,$campaign));
        return 1;
    }
    public function adminreject($id)
    {
        $data=CampaignInflList::find($id);
        $user=User::where('id',$data->iid)->first();
        $campaign=Campaign::where('id',$data->cid)->first();
        $data->status= 5;
        $data->save();
        Mail::to($user->email)->send(new ApplicationRejected($data,$campaign));
        return 1;
    }
    public function shortlist(Request $request,$id)
    {
        
        $data=CampaignInflList::find($id);
        $user=User::where('id',$data->iid)->first();
        $campaign=Campaign::where('id',$data->cid)->first();
        $data->status=$request->status;
        $data->contenttype=$request->contenttype;
        $data->otherdetails=$request->otherdetails;
        $data->commercial=$request->commercial;
        $data->save();
         Mail::to($user->email)->send(new ApplicationShortlisted($data,$campaign));
        return 1;
   
    }
    public function revisedoffer(Request $request,$id)
    {
   
        $data=CampaignInflList::find($id);
        $user=User::where('id',$data->iid)->first();
        $campaign=Campaign::where('id',$data->cid)->first();
        $data->status=$request->status;
        // $data->contenttype=$request->contenttype?json_encode($request->contenttype):null;
        $data->contenttype=$request->contenttype;
        $data->otherdetails=$request->otherdetails;
        $data->commercial=$request->commercial;
        $data->save();
         Mail::to($user->email)->send(new NewOfferReceived($data,$campaign));
        return 1;
   
    }
    public function posturl(Request $request,$id)
    {
        $data=CampaignInflList::find($id);
        $data->posturl=$request->posturl;
        $data->save();
        return 1;
    }
    public function ratecreator(Request $request,$id)
    {
        $data=CampaignInflList::find($id);
        $data->ratecreator=$request->ratecreator;
        $data->save();
        return 1;
    }
    public function contenttype(Request $request,$id)
    {
        $data=CampaignInflList::find($id);
        $data->contenttype=$request->contenttype;
        $data->save();
        return 1;
    }
    public function changestatus(Request $request,$id)
    {
        $data=CampaignInflList::find($id);
        $user=User::where('id',$data->iid)->first();
        $campaign=Campaign::where('id',$data->cid)->first();
        $data->status=$request->status;
        if ($request->status == 13) {
            Mail::to($user->email)->send(new CommercialsReceived($data,$campaign));
            $data->save();
            return 1;
        }
        $data->save();
        return 1;
     
    }

    public function updatein(Request $request,$id)
    {
        
        $data=CampaignInflList::find($id);
        $insta = InstagramSocial::where('iid',$data->iid)->first();
        $data->views=$request->views;
        $data->likes=$request->likes;
        $data->impressions=$request->impressions;
        $data->comments=$request->comments;
        $data->save=$request->save;
        $data->share=$request->share;
        $data->reach=$request->reach;
        $data->ctr=$request->ctr;
        if ($insta->ifollowers == 0) {
            $data->engratew= 0;
        }
        else {
            $data->engratew = ( ( ($request->views + $request->likes + $request->comments + $request->save + $request->share) / $insta->ifollowers ) *100);
        }
         
        if ($insta->ifollowers == 0) {
            $data->engratewout= 0;
        }
        else {
            $data->engratewout = ( ( ($request->likes + $request->comments + $request->save + $request->share) / $insta->ifollowers ) *100);
        }

        if ($request->impressions == 0 ) {
            $data->cpm= 0;
        }
        else {
            $data->cpm= ( ($data->commercial / $request->impressions ) * 1000 );
        }
        
        $data->save();
        return 1;
    }
   
    public function proorder(Request $request,$id)
    {
        $data=CampaignInflList::find($id);
        $data->orderid=$request->orderid;
        $data->pdeldate=$request->pdeldate;
        $data->status= 14;
        $data->save();
        return 1;
    }
  


   

}
