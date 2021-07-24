<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OtherOpp;
use Validator;

class OtherOppController extends Controller
{
    public function index(Request $request)
    {
       $otheropp = OtherOpp::select('otitle','odes','oppstatus','id','created_at');   
       if($request->otitle){
        $otheropp=$otheropp->where('otitle','LIKE','%'.$request->otitle.'%');
    }
    if($request->ids){
        $otheropp=$otheropp->where('id','LIKE','%'.$request->ids.'%');
    }

    $otheropp=$otheropp->paginate();
       $inact = OtherOpp::where('oppstatus','=',1)->get();
       $act = OtherOpp::where('oppstatus','=',0)->get();

   
        return view('Admin/OtherOpp/list',['otheropp'=>$otheropp,'inact'=>$inact,'act'=>$act]);
    }
    public function addopp()
    {     
        return view('Admin/OtherOpp/add');
    }
    public function storeopp(Request $request){
        // return $request;
       $validate = Validator::make($request->all(),[
           'otitle' => 'required|max:120',
           'odes' => 'required|max:1000',
           'ocontactus' => 'required',
     
          
       ],
       [
        'odes.max' => 'Description should not be more than 1000 characters.',
        'otitle.max' => 'Title should not be more than 120 characters.',
       
    ]
);
       if ($validate->fails()) {
           return back()->withInput()->withErrors($validate);
       }
      
       $opp=new OtherOpp;
       $opp->otitle=$request->otitle;
       $opp->odes=$request->odes;
       $opp->ocontactus=$request->ocontactus;
       $opp->oppstatus=0;
       $opp->save();
      
       $request->session()->flash('status', 'Opportunity Created Succesfully');
       return redirect('/admin/otheropp');

   }
   public function viewopp($id)
   {     
       $data = OtherOpp::find($id);
       return view('Admin/OtherOpp/view',['data'=>$data]);
   }
   public function editopp($id)
    {     
        $opp = OtherOpp::find($id);
        return view('Admin/OtherOpp/edit',['opp'=>$opp]);
    }
  
   public function updateopp(Request $request,$id)
   {

      
            $validate = Validator::make($request->all(),[
                'otitle' => 'required|max:120',
                'odes' => 'required|max:1000',
                'ocontactus' => 'required',
        
                    
            ],
            [
            'odes.max' => 'Description should not be more than 1000 characters.',
            'otitle.max' => 'Title should not be more than 120 characters.',
            
        ]
        );
           if ($validate->fails()) {
               return back()->withInput()->withErrors($validate);
           }
           
           $opp=OtherOpp::find($id);
            $opp->otitle=$request->otitle;
            $opp->odes=$request->odes;
            $opp->ocontactus=$request->ocontactus;
            $opp->oppstatus=0;
            $opp->save();

         

           $opp->save();
           $request->session()->flash('status', 'Edited Successfully');
         
          
         return redirect('/admin/otheropp');
   }
   public function deleteopp($id,Request $request)
   {
      
           $data=OtherOpp::find($id);
           $data->delete();
           $request->session()->flash('status', 'Deleted Successfully');
       
      
       return redirect('/admin/otheropp');
   }
   public function changes(Request $request,$id)
    {
        $data=OtherOpp::find($id);
        $data->oppstatus= 1;
        $data->save();
        return 1;
    }
    public function changein(Request $request,$id)
    {
        $data=OtherOpp::find($id);
        $data->oppstatus= 0;
        $data->save();
        return 1;
    }

}
