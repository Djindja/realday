<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Busstop extends Model
{

    /**
     * find closest busstops
     */
    public static function findClosest($latitude, $longitude, $limit = 5)
    {
        return self::selectRaw("id, name, lat, lon,
        ( 6371000 * acos( cos( radians(?) ) *
          cos( radians( lat ) )
          * cos( radians( lon ) - radians(?)
          ) + sin( radians(?) ) *
          sin( radians( lat ) ) )
        ) AS distance", [$latitude, $longitude, $latitude])
        ->orderBy("distance",'asc')
        ->limit($limit)
        ->get();
    }

}