<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;

class LogSuccessfulLogin
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(Login $event): void
    {
        if ($event->user->hasAnyRole(['admin', 'super-admin'])) {
            $event->user->loginActivities()->create([
                'ip_address' => $this->request->ip(),
                'user_agent' => $this->request->userAgent(),
            ]);
        }
    }
}