<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShortlistInf extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $data,$aurl,$rurl;
    
    public function __construct($data)
    {
        $this->data = $data;
        $this->aurl = config("app.url")."/acceptrequest/".encrypt($data->id)."/".encrypt(3);
        $this->rurl = config("app.url")."/acceptrequest/".encrypt($data->id)."/".encrypt(4);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.shortlistinf');
    }
}
