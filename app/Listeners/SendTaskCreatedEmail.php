<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use App\Mail\TasksCreated;
use Auth;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendTaskCreatedEmail implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TaskCreated $event): void
    {
        $email = new TasksCreated( $event->title);

        //$sendTime = new \DateTime();
        //$sendTime->modify('10 seconds');
        $sendTime = now(10);
        //Mail::to(Auth::user())->queue($email);
        Mail::to($event->email)->later($sendTime,$email);
    }
}
