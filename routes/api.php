<?php

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:sanctum']], function () {
    // Campaign
    Route::apiResource('campaigns', 'CampaignApiController');

    // Number
    Route::get('numbers/getNumbers/{campaign}/', 'NumberApiController@getNumbers');
    Route::apiResource('numbers', 'NumberApiController');
});
