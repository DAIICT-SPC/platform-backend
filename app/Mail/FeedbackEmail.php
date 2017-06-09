<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FeedbackEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $description;
    public $rating;
    public $placement;
    public $name;


    public function __construct($data)
    {
        $this->email = $data['email'];
        $this->description = $data['description'];
        $this->rating = $data['rating'];
        $this->placement = $data['placement'];
        $this->name = $data['name'];
    }

    public function build()
    {
        return $this->markdown('mail.feedback_email')
                    ->from("$this->email", "$this->name")
                    ->subject('Feedback: Placement Drive')
                    ->with([
                        'description' => $this->description,
                        'rating' => $this->rating,
                        'job_title' => $this->placement['job_title'],
                        'name' => $this->name,
                    ]);

    }
}
