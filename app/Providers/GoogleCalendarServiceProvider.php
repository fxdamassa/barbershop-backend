<?php

namespace App\Providers;

use Google\Client;
use Google\Service\Calendar;
use Illuminate\Support\ServiceProvider;

class GoogleCalendarServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Calendar::class, function ($app) {
            $client = new Client();
            $client->setAuthConfig(storage_path('app\credentials.json'));
            $client->addScope(Calendar::CALENDAR);

            if(session()->has('google_token')){
                $accessToken = session()->get('google_token');
                $client->setAccessToken($accessToken);
            }
            return new Calendar($client);
        });
    }
}
