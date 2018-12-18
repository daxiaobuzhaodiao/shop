<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Events\Registered;
use App\Notifications\EmailVerificationNotification;

class RegisterListener implements ShouldQueue
{

    public function __construct()
    {
        //
    }

    
    
    public function handle(Registered $event)
    {
        $user = $event->user;
        
        // new EmailVerificationNotification($user);
        $user->notify(new EmailVerificationNotification());
    }
}
