<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Socialite;
use App\User;
use Auth;

class GithubController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('github')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
    	$githubUser = Socialite::driver('github')->user();
        
        //check user from database
        $user = User::where('provider_id', $githubUser->getId())->first();
        //not register with github
        if (!$user) {
        	//create user with github details
	        $user = User::create([
	        	// All Providers
				'provider_id' => $githubUser->getId(),
				'provider_name' => 'github',
				'name' => $githubUser->getName(),
				'email' => $githubUser->getEmail(),
	        ]);
        }
        //authenticate user
        Auth::login($user, true);

        return redirect('/home');
    }
}
