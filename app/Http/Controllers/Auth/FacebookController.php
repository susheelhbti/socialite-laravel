<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Socialite;
use App\User;
use Auth;

class FacebookController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
    	$facebookUser = Socialite::driver('facebook')->user();
   
        //check user from database
        $user = User::where('provider_id', $facebookUser->getId())->first();
        //not register with facebook
        if (!$user) {
        	if ($facebookUser->getName() == '' OR $facebookUser->getEmail() == '') {
        		$notification = [
        			'message' => 'Please, Change access permission. We can not found your name or email',
        			'alert-type' => 'error'
        		];
	        	return redirect('/login')->with($notification);
	        } else {
	        	//create user with facebook details
		        $user = User::create([
		        	// All Providers
					'provider_id' => $facebookUser->getId(),
					'provider_name' => 'facebook',
					'name' => $facebookUser->getName(),
					'email' => $facebookUser->getEmail(),
		        ]);
	        }
        }
        //authenticate user
        Auth::login($user, true);

        return redirect('/home');
    }
}
