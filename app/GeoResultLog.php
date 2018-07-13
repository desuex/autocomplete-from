<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * @property string result1
 * @property string result2
 * @property string result3
 * @property string result4
 * @property string result5
 * @property string query
 * @property string ip
 * @property boolean served_from_db
 *
 */
class GeoResultLog extends Model
{
    protected $fillable = [
        'query',
        'result1',
        'result2',
        'result3',
        'result4',
        'result5',
        'ip',
        'served_from_db',
    ];

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });

    }
    public function setResults(GeoResult $geoResult)
    {
        $this->result1 = $geoResult->result1;
        $this->result2 = $geoResult->result2;
        $this->result3 = $geoResult->result3;
        $this->result4 = $geoResult->result4;
        $this->result5 = $geoResult->result5;
    }

}
