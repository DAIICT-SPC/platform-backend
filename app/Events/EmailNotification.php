<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class EmailNotification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $email;

    public function __construct($email)
    {
        $this->email = $email;
    }

    public function sendMail()
    {
        //code to send mail
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
