<?php

use Google\Client;
use Illuminate\Http\Request;

class GoogleController extends Controller
{
    public function redirectoGoogle(){
        $client = new Client();
        $client->setAuthConfig('app\credentials.json');
        $client->addScope(\Google\Service\Calendar::CALENDAR);
        $client->setRedirectUri(route('google.callback'));

        $authUrl = $client->createAuthUrl();
        return redirect($authUrl);
    }

    public function handleGoogleCallback(Request $request){
        $client = new Client();
        $client->setAuthConfig('app\credentials.json');
        $client->authenticate($request->input('code'));

        $token = $client->getAccessToken();
        session(['access_token' => $token]);

        return redirect()->route('dashboard');
    }
}
