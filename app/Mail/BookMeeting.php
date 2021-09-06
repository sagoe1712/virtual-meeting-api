<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookMeeting extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

     protected $email_data;
    public function __construct($email_data)
    {
        //
        $this->email_data = $email_data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // dd($this->email_data);
        return $this->view('email')->subject("Re: Your Booked Meeting With {$this->email_data['user_first_name']}")
                    ->with([
                        'first_name'=> $this->email_data['first_name'],
                        'last_name'=> $this->email_data['last_name'],
                        'booking_email' =>$this->email_data['booking_email'],
                        'booking_phone' =>$this->email_data['booking_phone'],
                        'meeting_date'=> $this->email_data['meeting_date'],
                        'meeting_channel'=> $this->email_data['meeting_channel'],
                        'meeting_url'=> $this->email_data['meeting_url'],
                        'company_name'=>$this->email_data['company_name'],
                        'user_first_name'=>$this->email_data['user_first_name'],
                        'user_last_name'=>$this->email_data['user_last_name'],
                    ]);
    }
}
