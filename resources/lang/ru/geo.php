<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 13.07.2018
 * Time: 16:13
 */


return [
    'errors'=>[
        'not_ready_to_perform_request' => 'Сервис не готов отправить запрос!',
        'not_ready_to_handle_result' => 'Сервис не готов обработать результат!',


    ],
    'statuses'=>
    [
        \App\GeoService::STATUS_NOT_READY=> "Не готово",
        \App\GeoService::STATUS_PERFORMING_REQUEST=> "Выполняю запрос",
        \App\GeoService::STATUS_READY=> "Готов выполнить запрос",
        \App\GeoService::STATUS_COMPLETE=> "Запрос выполнен",
        \App\GeoService::STATUS_JSON_ERROR=> "Ошибка: Невозможно обработать данные JSON",
        \App\GeoService::STATUS_HTTP_ERROR=> "Ошибка: Невозможно выполнить HTTP-запрос",
        \App\GeoService::RESULT_NOT_READY=> "Результат еще не был обработан",
        \App\GeoService::RESULT_ZERO_RESULTS=> "0 результатов",
        \App\GeoService::RESULT_REQUEST_DENIED=> "Доступ запрещён",
        \App\GeoService::RESULT_OK=> "Ok",


    ]
];