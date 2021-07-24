<?php

namespace App\Exports;

use App\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class InfluencerExport implements FromView
{
    public function view(): View
    {
        return view('Admin/Influencer/list', [
            'user' => User::with('influencers')->with('instagrams')->where('type',0)->get()
        ]);
    }
}
