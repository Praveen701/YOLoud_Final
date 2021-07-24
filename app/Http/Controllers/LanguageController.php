<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Language;

class LanguageController extends Controller
{
    public function index()
    {
        $data=Language::select('id','name')->get();
        return view('Admin/settings/language',['title'=>'Languages','data'=>$data]);
    }
    public function store(Request $request)
    {
        $data=new Language;
        $data->name=$request->name;
        $data->save();
        return response()->json(['type'=>1,'data'=>$data]);
        return 1;
    }
    public function update(Request $request,$id)
    {
        $data=Language::find($id);
        $data->name=$request->name;
        $data->save();
        return 1;
    }
    public function destroy(Request $request)
    {
        Language::find($request->id)->delete();
        return 1;
    }
}
