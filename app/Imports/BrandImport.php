<?php

namespace App\Imports;

use App\Brand;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
HeadingRowFormatter::default('none');

class BrandImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $cat=Brand::where('email',$row['Email'])->first();
        if(!$cat){
            $cat=new Brand;
            $cat->name=$row['Name'];
            $cat->email=$row['Email']; 
            $cat->companyname=$row['Companyname'];
            $cat->designation=$row['Designation'];  
            $cat->city=$row['City'];
            $cat->state=$row['State']; 
            $cat->country=$row['Country'];
            $cat->pincode=$row['Pincode'];
            $cat->phonenumber=$row['Phonenumber']; 
            $cat->offering=$row['Offering'];
            $cat->phonestatus=$row['Phonestatus'];
             $cat->emailstatus=$row['Emailstatus'];
            $cat->save();
        }
        return $cat;
     
    }
}
