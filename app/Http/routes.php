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
            return response()->json(['response' => 'you POSTed plate: ' . Input::get('plate')]);
        });
    });
});