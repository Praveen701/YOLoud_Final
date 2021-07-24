<?php

namespace App\Exports;

use App\Brand;
use Maatwebsite\Excel\Concerns\FromCollection;
class BrandExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Brand::select('name','email','companyname','designation','city','state','country','pincode','phonenumber','offering','created_at')->get();
    }

}
