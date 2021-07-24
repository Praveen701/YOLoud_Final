<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Brand;
use App\Exports\BrandExport;
use Excel;

class BrandController extends Controller
{
   public function index(){
       
       return view('Brand/view');
   }
   public function export(Request $request) 
   {
       return Excel::download(new BrandExport, 'brand.xlsx');
   }
}
