<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 13.07.2018
 * Time: 16:13
 */


return [
    'errors'=>[
        'not_ready_to_perform_request' => 'Not ready to perform request!',
        'not_ready_to_handle_result' => 'Not ready to handle result!',


    ],
    'statuses'=>
    [
        \App\GeoService::STATUS_NOT_READY=> "Not ready",
        \App\GeoService::STATUS_PERFORMING_REQUEST=> "Performing request",
        \App\GeoService::STATUS_READY=> "Ready to perform the request",
        \App\GeoService::STATUS_COMPLETE=> "Request completed",
        \App\GeoService::STATUS_JSON_ERROR=> "Error: Failed to parse JSON data",
        \App\GeoService::STATUS_HTTP_ERROR=> "Error: Failed to perform HTTP request",
        \App\GeoService::RESULT_NOT_READY=> "Result not yet been parsed",
        \App\GeoService::RESULT_ZERO_RESULTS=> "Zero results",
        \App\GeoService::RESULT_REQUEST_DENIED=> "Request denied",
        \App\GeoService::RESULT_OK=> "Ok",


    ]
];