<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Config;

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
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        $mail_options = \App\Options::get('mail');
        if (is_object($mail_options)) {
            Config::set('mail.host', $mail_options->hostname);
            Config::set('mail.port', $mail_options->port);
            Config::set('mail.encryption', $mail_options->encryption);
            Config::set('mail.username', $mail_options->username);
            Config::set('mail.password', $mail_options->password);
            Config::set('mail.from.address', $mail_options->sendermail);
            Config::set('mail.from.name', $mail_options->sendername);
        }
        parent::boot();
    }
}
