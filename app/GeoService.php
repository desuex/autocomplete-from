<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 13.07.2018
 * Time: 15:26
 */

namespace App;



class GeoService
{
    protected $apiKey;
    protected $baseUrl;
    protected $queryString;
    protected $input;
    protected $status;
    protected $error;
    protected $errorMessage;
    protected $resultStatus;
    protected $result;
    protected $predictions = [];
    protected $count;

    public const STATUS_NOT_READY = 0;
    public const STATUS_READY = 1;
    public const STATUS_PERFORMING_REQUEST = 2;
    public const STATUS_COMPLETE = 3;
    public const STATUS_JSON_ERROR = -1;
    public const STATUS_HTTP_ERROR = -2;

    public const RESULT_NOT_READY = 100;
    public const RESULT_OK = 101;
    public const RESULT_ZERO_RESULTS = 102;
    public const RESULT_REQUEST_DENIED = 103;

    /**
     * GeoService constructor.
     * @param null $apiKey
     */
    public function __construct($apiKey = null)
    {

        if ($apiKey !== null) {
            $this->apiKey = $apiKey;
        } else {
            $this->apiKey = \config('app.google_api_key');
        }

        $this->baseUrl = "https://maps.googleapis.com/maps/api/place/autocomplete/json?";
    }

    /**
     * @param $input
     * @return array Predictions
     * @throws GeoException
     */
    public function get($input)
    {
        $this->reset();
        $this->input = $input;
        $this->buildQuery();
        $requestSuccess = $this->apiRequest();
        if ($requestSuccess) {
            $this->handleResult();
        }
        return $this->predictions;

    }
    public function count()
    {
        return $this->count;
    }

    /**
     * @return string Status for log message
     */
    public function status()
    {
        $httpStatus = __('geo.statuses.'.$this->status);
        $resultStatus = __('geo.statuses.'.$this->resultStatus);

        return "HTTP: $httpStatus | RESULT: $resultStatus | COUNT: ".$this->count ." | ERROR MESSAGE: ".$this->errorMessage;
    }

    public function hasErrors()
    {
        return $this->error;
    }

    public function reset()
    {
        $this->status = self::STATUS_NOT_READY;
        $this->error = false;
        $this->input = null;
        $this->queryString = null;
        $this->result = null;
        $this->resultStatus = self::RESULT_NOT_READY;
        $this->predictions = [];
        $this->count = 0;
        $this->errorMessage = null;
    }

    protected function buildQuery()
    {
        $params = [
            'key' => $this->apiKey, 'input' => $this->input
        ];
        $this->queryString = $this->baseUrl . http_build_query($params);
        $this->status = self::STATUS_READY;

    }

    /**
     * return bool
     * @throws GeoException
     */
    protected function apiRequest()
    {
        if ($this->status !== self::STATUS_READY) {
            $this->error = true;
            throw new GeoException(__('geo.errors.not_ready_to_perform_request'));
        }
        $this->status = self::STATUS_PERFORMING_REQUEST;
        $ch = curl_init($this->queryString);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_URL, $this->queryString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        $info = curl_getinfo($ch);
        $http_result = $info ['http_code'];
        curl_close($ch);
        if ($http_result !== 200) {
            $this->error = true;
            $this->status = self::STATUS_HTTP_ERROR;
            dd($info);
            return false;
        }
        $json = json_decode($output);
        if (!$json) {
            $this->error = true;
            $this->status = self::STATUS_JSON_ERROR;
            return false;
        }
        $this->result = $json;
        $this->status = self::STATUS_COMPLETE;
        return true;


    }

    /**
     * @throws GeoException
     */
    protected function handleResult()
    {
        if ($this->status !== self::STATUS_COMPLETE) {
            $this->error = true;
            throw new GeoException(__('geo.errors.not_ready_to_handle_result'));
        }

        switch ($this->result->status){
            case "OK": {
                $this->resultStatus = self::RESULT_OK;

                foreach ($this->result->predictions as $prediction) {
                    $this->count++;
                    $this->predictions[] = $prediction->description;
                }
                break;
            }
            case "REQUEST_DENIED": {
                $this->error = true;
                $this->resultStatus = self::RESULT_REQUEST_DENIED;
                break;
            }
            case "ZERO_RESULTS": {
                $this->resultStatus = self::RESULT_ZERO_RESULTS;
                break;
            }
        }
        if(isset($this->result->error_message)){
            $this->errorMessage = $this->result->error_message;
        }



    }
}