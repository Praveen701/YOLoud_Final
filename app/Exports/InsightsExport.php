<?php

namespace App\Exports;

use App\CampaignInflList;
use Maatwebsite\Excel\Concerns\FromCollection;

class InsightsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return CampaignInflList::all();
    }
}
