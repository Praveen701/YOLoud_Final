<?php

namespace App\Imports;


use App\InstagramSocial;
use App\User;
use App\Influencer;
use Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


HeadingRowFormatter::default('none');

class InfluencersImport implements ToModel, WithHeadingRow
{
 

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $cat=User::where('email',$row['emails'])->first();

        if(!$cat)
        {

            $cat=new User;
            $cat->name=$row['Name'];
            $cat->email=$row['emails']; 
            $cat->UID=$row['UID']; 
            $cat->password=Hash::make('YOLOUD001'); 
            $cat->type=0;
            $cat->notification_preference='mail, database'; 
            $cat->status=$row['Status']; 
            $cat->accountstatus=$row['AccountStatus']; 
            $cat->verified=$row['Verified']; 
            $cat->save();
          

            $inf=new Influencer;
            $inf->iid=$cat->id;
            // $inf->PID=$row['PID'];
            $inf->platform=$row['Platform'];
            $inf->phone=$row['Phone'];
            $inf->dob= date('Y-m-d',strtotime($row['Date']));
            $inf->gender=$row['Gender'];  
            $inf->city=$row['City']; 
            $inf->type=$row['Type']; 
            $inf->intrest=$row['Category'];
            $inf->primarycategory=$row['PrimaryCategory'];
            $inf->intrest=$row['Category'];
            $inf->emailstatus=$row['emailstatus'];
            $inf->phonestatus=$row['phonestatus'];
            $inf->categorystatus=$row['categorystatus'];
            $inf->save();

        
            $insta=new InstagramSocial;
            $insta->iid=$cat->id;
            $insta->iusername=$row['Username'];  
            $insta->iaudienceloc=$row['Audience Location'];  
            $insta->iaudienceage=$row['Audience Age']; 
            $insta->iaudiencegen=$row['Audience Gender'];   
            $insta->ifollowers=$row['Followers'];   
            $insta->iposts=$row['Posts'];   
            $insta->iavglike=$row['Average Likes'];   
            $insta->iavgcmt=$row['Average Comments'];
            $insta->iengagementrate=$row['Engagement Rate'];   
            $insta->iqs=$row['Quality Score'];   
            $insta->ipfr=$row['Post : Follower Ratio'];        
            $insta->save();
    }
  
    return $cat;
  
 }

}
