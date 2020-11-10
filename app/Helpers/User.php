<?php

namespace App\Helpers;

class User {

    public static function returnListIds($array) :string
    {
        $a = [];

        foreach ($array as $e) {
            $a[] = $e->id;
        }

        return implode(', ', $a);
    }
}