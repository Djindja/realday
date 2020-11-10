<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Postcode extends Model
{
    public static function findInRadius($latitude, $longitude, $radius = 10 )
    {
        return self::selectRaw("id, postcode, latitude, longitude,
        ( 6371000 * acos( cos( radians(?) ) *
          cos( radians( latitude ) )
          * cos( radians( longitude ) - radians(?)
          ) + sin( radians(?) ) *
          sin( radians( latitude ) ) )
        ) AS distance", [$latitude, $longitude, $latitude])
        ->orderBy("distance",'asc')
        ->having("distance", "<", $radius)
        ->get();
    }
}