<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/autocomplete', function (Request $request) {

    $queryOriginal = $request->get("q");
    $query = mb_strtolower($queryOriginal);
    $log = new \App\GeoResultLog();
    $log->query=$queryOriginal;
    $log->ip = $request->ip();
    /*
     * If we can find exact match in DB
     */
    $resultExact =\App\GeoResult::findExact($query);
    if($resultExact instanceof \App\GeoResult){
        $log->served_from_db = true;
        $log->setResults($resultExact);
        $log->save();
        return $resultExact->toArr();
    }
    /*
     * If we can find something that looks like user input in DB
     */
    $resultLike =\App\GeoResult::findLike($query);
    if($resultLike instanceof \App\GeoResult){
        $log->served_from_db = true;
        $log->setResults($resultLike);
        $log->save();
        return $resultLike->toArr();
    }
    /*
     * Otherwise we're making an API call
     */
    $geoService = new \App\GeoService();
    $result = $geoService->get($query);
    if(!$geoService->hasErrors()){

        $data = [
            'query'=>$query,
            'ip'=>$request->ip(),
            'successful'=>!!$geoService->count()
        ];
        $geoResult = new \App\GeoResult($data);
        $geoResult->setResults($result);
        $geoResult->save();
        $log->setResults($geoResult);
        $log->save();
        return $geoResult->toArr();
    }
    return ['error'=>$geoService->status()];

});
Route::get('/logs',function(){
   return \App\GeoResultLog::all();
});