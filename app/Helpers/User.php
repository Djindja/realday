<?php

namespace App\Helpers;

class User {

    /**
     * return list of Ids
     */
    public static function returnListIds($array) :string
    {
        $a = [];

        foreach ($array as $e) {
            $a[] = $e->id;
        }

        return implode(', ', $a);
    }
}