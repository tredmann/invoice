<?php

namespace App\Http\Controllers\Datev;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use League\OAuth2\Client\Provider\GenericProvider;

class ConnectController
{
    private GenericProvider $provider;

    public function __construct()
    {
        $this->provider = new GenericProvider([
            'clientId'                => config('datev.clientId'),
            'clientSecret'            => config('datev.clientSecret'),
            'redirectUri'             => route('datev.authorize'),
            'urlAuthorize'            => config('datev.urlAuthorize'),
            'urlAccessToken'          => config('datev.urlAccessToken'),
            'urlResourceOwnerDetails' => config('datev.urlResourceOwnerDetails'),
            'pkceMethod' => GenericProvider::PKCE_METHOD_S256
        ]);
    }

    public function login()
    {
        $url = $this->provider->getAuthorizationUrl(
            [
                'scope' => 'openid accounting:documents accounting:clients',
                'response_type' => 'code id_token',
                'response_mode' => 'query',
                'approval_prompt' => null,
                'nonce' => Str::random(20)
            ]
        );

        session()->put('pkce', $this->provider->getPkceCode());
        session()->put('state', $this->provider->getState());



        return redirect($url);
    }

    public function authorize(Request $request)
    {
        ##after login
        if ($request->has('code')) {
            $code = $request->get('code');

            $accessTokenResponse = \Http::withBasicAuth(config('datev.clientId'), config('datev.clientSecret'))
                ->withHeaders(['Content-Type' => 'application/x-www-form-urlencoded'])
                ->post(
                    config('datev.urlAccessToken'),
                    [
                        'grant_type' => 'authorization_code',
                        'redirect_url' => route('datev.authorize'),
                        'code' => $code,
                        'code_verifier' => session()->get('pkce')
                    ]
                );




            dump($accessTokenResponse->json());
        }
    }
}
