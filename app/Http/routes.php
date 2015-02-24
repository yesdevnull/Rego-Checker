<?php

Route::get('/', function()
{
    $encrypted_csrf_token = Crypt::encrypt(csrf_token());

    return view('home')->withEncryptedCsrfToken($encrypted_csrf_token);
});

Route::get('/search', function() {
    $encrypted_csrf_token = Crypt::encrypt(csrf_token());

    return view('home')->withEncryptedCsrfToken($encrypted_csrf_token);
});

Route::group(['prefix' => 'api'], function() {
    Route::group(['prefix' => 'v1'], function() {
        Route::get('/', function() {
            return 'hello';
        });

        Route::post('/plate', function() {
            $checker = new RegoCheck;

            $plateCheck = $checker->plateCheck('wa', Input::get('plate'));

            return response()->json(['response' => $plateCheck]);
        });
    });
});