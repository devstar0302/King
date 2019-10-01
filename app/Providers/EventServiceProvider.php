<?php

namespace App\Providers;

use App\Events\ParagraphSaved;
use App\Listeners\ParagraphSaved\ChangeMalfunctionRisk;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ParagraphSaved::class => [
              ChangeMalfunctionRisk::class
        ]
    ];

    public function boot()
    {
        parent::boot();

        //
    }
}
