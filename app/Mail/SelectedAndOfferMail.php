<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SelectedAndOfferMail extends Mailable
{
    use Queueable, SerializesModels;

    public $job_title;
    public $location;
    public $company_name;
    public $job_type_name;
    public $offer;

    public function __construct($data)
    {
        $this->job_title = $data['job_title'];
        $this->location = $data['location'];
        $this->company_name = $data['company_name'];
        $this->job_type_name = $data['job_type_name'];
        $this->offer = $data['offer'];

    }

    public function build()
    {
        return $this->markdown('mail.selectedandoffered')
                    ->from('spc@daiict.ac.in')
                    ->subject('SPC - You are Selected!')
                    ->with([

                        'job_title' => $this->job_title,
                        'location' => $this->location,
                        'company_name' => $this->company_name,
                        'job_type_name' => $this->job_type_name,
                        'offer' => $this->offer,

                    ]);

    }
}
