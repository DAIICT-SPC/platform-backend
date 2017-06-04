<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SelectedForRound1Email extends Mailable
{
    use Queueable, SerializesModels;

    public $job_title;
    public $location;
    public $company_name;
    public $job_type_name;
    public $round_no;
    public $round_name;

    public function __construct($data)
    {

        $this->job_title = $data['job_title'];
        $this->location = $data['location'];
        $this->company_name = $data['company_name'];
        $this->job_type_name = $data['job_type_name'];
        $this->round_no = $data['round_no'];
        $this->round_name = $data['round_name'];

    }

    public function build()
    {
        return $this->markdown('mail.selectedforroundone')
                    ->from('spc@daiict.ac.in')
                    ->subject('SPC - You are Selected!')
                    ->with([

                        'job_title' => $this->job_title,
                        'location' => $this->location,
                        'company_name' => $this->company_name,
                        'job_type_name' => $this->job_type_name,
                        'round_name' => $this->round_name,
                        'round_no' => $this->round_no,

                    ]);
    }
}
