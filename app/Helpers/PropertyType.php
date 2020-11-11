<?php

namespace App\Helpers;

class PropertyType {

    /**
     * converting property type from houses table
     */
    public static function propertyTypeConvert($type)
    {
        switch ($type) {
            case '1':
                return 'FLAT';
            break;
            case '2':
                return 'small house';
            break;
            case '3':
                return 'big house';
            break;
            case '4':
                return 'Villa';
            break;
            case '0':
                return '-';
            break;
        }
    }
}