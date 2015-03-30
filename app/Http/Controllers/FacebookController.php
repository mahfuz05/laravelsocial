<?php
/**
 * Created by PhpStorm.
 * User: mahfuz
 * Date: 2/24/15
 * Time: 3:10 AM
 */

namespace Mahfuz\Http\Controllers;





class FacebookController extends Controller{


    public function index(\SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
    {
        // Obtain an access token.
        try {
            $token = $fb->getAccessTokenFromRedirect();
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            dd($e->getMessage());
        }

         //$token will be null if the user hasn't authenticated your app yet
        if (! $token) {
            $helper = $fb->getRedirectLoginHelper();

            if (! $helper->getError()) {
                abort(403, 'Unauthorized action.');
            }
            // User denied the request
            dd(
                $helper->getError(),
                $helper->getErrorCode(),
                $helper->getErrorReason(),
                $helper->getErrorDescription()
            );
        }

        if (! $token->isLongLived()) {
               // OAuth 2.0 client handler
                $oauth_client = $fb->getOAuth2Client();

               // Extend the access token.
                try {
                    $token = $oauth_client->getLongLivedAccessToken($token);
               } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                   dd($e->getMessage());
                }
        }

        $fb->setDefaultAccessToken($token);

        // Save for later
        Session::put('fb_user_access_token', (string) $token);

        // Get basic info on the user from Facebook.
        try {
            $response = $fb->get('/me?fields=id,name,email');
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
               dd($e->getMessage());
        }

        // Convert the response to a `Facebook/GraphNodes/GraphUser` collection
        $facebook_user = $response->getGraphUser();


        $user = \Mahfuz\User::createOrUpdateUser($facebook_user);

        // Log the user into Laravel
        Auth::login($user);

        return redirect('/')->with('message', 'Successfully logged in with Facebook');

    }

    public function fbLogin(\SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb){
        // Send an array of permissions to request
        $login_url = $fb->getLoginUrl(['email']);

        //redirect to facebook for login
        return redirect($login_url);
    }

    public function get(){
        $faker = \Faker\Factory::create();
        return view('facebook.fb', ['faker' => $faker]);
    }
} 