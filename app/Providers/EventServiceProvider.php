<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        \Illuminate\Auth\Events\Login::class => [
            \App\Listeners\UpdateLastLogin::class,
        ],

        \App\Events\ChangeRequestCreated::class => [
            \App\Listeners\SendChangeRequestNotifications::class,
        ],
        \App\Events\ChangeRequestStatusUpdated::class => [
            \App\Listeners\SendChangeRequestNotifications::class,
        ],
        \App\Events\ChangeRequestUserAssignment::class => [
            \App\Listeners\SendChangeRequestNotifications::class,
        ],
        \App\Events\StatusChanged::class => [
            \App\Listeners\HandleStatusChange::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
        parent::boot();

        \Event::listen(Login::class, function ($event) {
            DB::table('sessions')
                ->where('id', session()->getId())
                ->update(['user_id' => $event->user->id]);
        });
    }
}
