<?php

namespace App\Providers;
use App\CampaignInflList;
use Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      
        // view()->composer(
        //     'layouts.app',     
        //     function ($view) {        
        //         $view->with('capplied', \App\CampaignInflList::where('iid',Auth::user()->id)->get())
        //         ->with('cpar', \App\CampaignInflList::where('iid',Auth::user()->id)->where('status','>',5)->get())
        //         ->with('ccom', \App\CampaignInflList::where('iid',Auth::user()->id)->where('status','>',13)->get());
        //       }  
            
        // );
           
       
    }
}


