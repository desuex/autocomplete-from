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
 */
class GeoResult extends Model
{
    protected $fillable = [
        'query',
        'result1',
        'result2',
        'result3',
        'result4',
        'result5',
    ];
    protected $hidden = ['id','query','created_at'];

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    public function setResults($predictions)
    {
        foreach ($predictions as $index=>$prediction){
            $prop = "result".($index+1);
            $this->$prop=$prediction;
        }
    }
    public function toArr(){
        $result = [];
        for($i = 1;$i<5;$i++){
            $prop = "result$i";
            if($this->$prop)
                $result[]=$this->$prop;
        }
        return $result;
    }
    public static function findExact($query)
    {
        return self::where('query', $query)->first();
    }
    public static function findLike($query)
    {
        return self::where('query', 'LIKE', $query . '%')->first();
    }
}
