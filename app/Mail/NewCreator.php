<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewCreator extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $data,$campaign,$url;
    
    public function __construct($data,$campaign)
    {
        $this->data = $data;
         $this->campaign = $campaign;
         $this->url = config("app.url")."/campaign/".encrypt($data->cid)."/newaccept/".encrypt($data->id)."/brand/".encrypt($data->bid);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.newcreator');
    }
}
