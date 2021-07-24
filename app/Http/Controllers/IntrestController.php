<?php

namespace App\Http\Controllers;

use App\Intrest;
use Illuminate\Http\Request;

class IntrestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data=Intrest::select('id','name')->get();
        return view('Admin/settings/intrests',['title'=>'Intrests','data'=>$data]);
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
    public function store(Request $request)
    {
        $data=new Intrest;
        $data->name=$request->name;
        $data->save();
        return response()->json(['type'=>1,'data'=>$data]);
        return 1;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Intrest  $intrest
     * @return \Illuminate\Http\Response
     */
    public function show(Intrest $intrest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Intrest  $intrest
     * @return \Illuminate\Http\Response
     */
    public function edit(Intrest $intrest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Intrest  $intrest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        $data=Intrest::find($id);
        $data->name=$request->name;
        $data->save();
        return 1;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Intrest  $intrest
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        Intrest::find($request->id)->delete();
        return 1;
    }
}
