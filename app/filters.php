<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	date_default_timezone_set('PRC');
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			return Redirect::guest('login');
		}
	}
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() !== Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

Route::filter('auth.user.is_in',function()
{
	if ( !Session::has( 'user.id' ) ){
		
		if ( Request::wantsJson() ){
	
			return Response::json(array('error_code' => 10, 'message' => '请登陆！'));
		}else{

			Session::put( 'uri.before_login', Request::path() );

			return Redirect::guest( 'user/login' );
		}
	}else if ( !Sentry::check() ){

		$user = Sentry::findUserById( Session::get( 'user.id' ) );

		Sentry::login( $user, false );
	}
});

Route::filter('weixin', function(){

	$signature = Input::get( 'signature' );
    $timestamp = Input::get( 'timestamp' );
	$nonce = Input::get( 'nonce' );
	            
    $tmpArr = array( 'ziruikeji', $timestamp, $nonce );
	sort( $tmpArr, SORT_STRING );
	$tmpStr = implode( $tmpArr );
	$tmpStr = sha1( $tmpStr );
	
	if ( $tmpStr != $signature ){
		return 'Fail';
	}
	//return $tmpStr == $signature;
});
