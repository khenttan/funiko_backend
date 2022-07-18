<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::redirect('/home', '/');

/*
|------------------------------------------
| Website
|------------------------------------------
*/

Route::group(['namespace' => 'Website','middleware'=>'revalidate'], function () {
    Route::get('/admin', function () {
        if(Auth::check()){
            return redirect()->route('admin');
        }
        else{
            return view('auth.login');
        }
    });

    Route::get('/', 'HomeController@index');
    
    Route::any('make_payment', 'PaymentController@makePayemt')->name('makepayment');

    Route::get('/news/{categorySlug?}', 'NewsController@index')->name('news');
    Route::get('/articles/{categorySlug}/{newsSlug}', 'NewsController@show');

    Route::get('/contact-us', 'ContactUsController@index')->name('contact');
    Route::post('/contact-us/submit', 'ContactUsController@feedback');

    // faq
    Route::namespace('FAQ')->group(function () {
        Route::get('/faq', 'FAQController@index');
        Route::post('/faq/question/{faq}/{type?}', 'FAQController@incrementClick');
    });

    // shop
    Route::group(['namespace' => 'Shop'], function () {
        Route::post('/products/filter', 'ShopController@filter');
        Route::get('/products/basket', 'BasketController@index');
        Route::post('/products/basket', 'BasketController@submitBasket');
        Route::get('/products/show/{productSlug}', 'ShopController@show');

        Route::group(['middleware' => ['auth']], function () {
            
            Route::get('/products/basket/address', 'BasketController@showAddress');
            Route::post('/products/basket/address', 'BasketController@submitAddress');
            Route::get('/products/basket/checkout', 'BasketController@showCheckout');
            Route::post('/products/basket/checkout', 'BasketController@submitCheckout');
            Route::get('/products/basket/checkout/feedback', 'BasketController@showCheckoutFeedback');
            Route::get('/products/basket/add/{product}/{quantity?}', 'BasketController@addProduct');
            Route::get('/products/basket/remove/{product}', 'BasketController@removeProduct');
        
        });

        Route::get('/products/{slugs?}', 'ShopController@index')->where('slugs', '(.*)');
    });
});


/*
|------------------------------------------
| Website Account
|------------------------------------------
*/
Route::group(
    ['middleware' => ['auth'], 'prefix' => 'account', 'namespace' => 'Website\Account'],
    function () {
        Route::get('/', 'AccountController@index')->name('account');
        Route::get('/profile', 'ProfileController@index')->name('profile');
        Route::post('/profile', 'ProfileController@update');
        Route::get('/orders', 'AccountController@transactions');
        Route::get('/orders/{reference}', 'AccountController@showTransaction');
        Route::get('/orders/{reference}/print', 'AccountController@printTransaction');

        Route::get('/address', 'ShippingAddressController@index');
        Route::post('/address', 'ShippingAddressController@update');
    }
);


Route::group(['middleware' => ['auth', 'auth.admin'], 'prefix' => 'admin', 'namespace' => 'Admin'], function () {
    Route::get('/', 'DashboardController@index')->name('admin');
});
/*
|------------------------------------------
| Authenticate User
|------------------------------------------
*/
Route::group(['prefix' => 'auth' ], function () {
   // Auth::routes(['verify' => true]);
    
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login')->middleware('guest');
Route::post('login', 'Auth\LoginController@login')->middleware(['revalidate','guest']);
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
Route::get('register', 'Auth\LoginController@showLoginForm')->name('register')->middleware('guest');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request')->middleware('guest');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email')->middleware('guest');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset')->middleware('guest');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update')->middleware('guest');

// Confirm Password (added in v6.2)
Route::get('password/confirm', 'Auth\ConfirmPasswordController@showConfirmForm')->name('password.confirm')->middleware('guest');
Route::post('password/confirm', 'Auth\ConfirmPasswordController@confirm')->middleware('guest');

// Email Verification Routes...
Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice')->middleware('guest');
Route::get('email/verify/{id}/{hash}', 'Auth\VerificationController@verify')->name('verification.verify')->middleware('guest'); // v6.x
/* Route::get('email/verify/{id}', 'Auth\VerificationController@verify')->name('verification.verify'); // v5.x */
Route::get('email/resend', 'Auth\VerificationController@resend')->name('verification.resend')->middleware('guest');
    
    Route::any('reset-password/{validatestring}', 'Auth\ForgotPasswordController@resetPassword')->name('user.resetpassword');
    Route::any('reset-password-update/{validatestring}', 'Auth\ForgotPasswordController@resetPasswordUpdate')->name('user.resetpasswordupdate');
    Route::any('auth/logout', 'Auth\LoginController@logout')->name('logout');
    Route::any('logout', 'Auth\LoginController@logout')->name('logout');
});

/*
|------------------------------------------
| Dynamic Pages - up to 3 slugs
|------------------------------------------
*/
Route::group(['namespace' => 'Website'], function () {
    Route::get('{slug1}/{slug2?}/{slug3?}', 'PagesController@index');
});


//Clear route cache:
Route::get('/route-cache', function() {
    $exitCode = Artisan::call('route:cache');
    return 'Routes cache cleared';
});

//Clear config cache:
Route::get('/config-cache', function() {
    $exitCode = Artisan::call('config:cache');
    return 'Config cache cleared';
}); 

// Clear application cache:
Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    return 'Application cache cleared';
});

// Clear view cache:
Route::get('/view-clear', function() {
    $exitCode = Artisan::call('view:clear');
    return 'View cache cleared';
});


