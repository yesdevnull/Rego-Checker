<?php

Route::get('/', function()
{
    return View::make('home');
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