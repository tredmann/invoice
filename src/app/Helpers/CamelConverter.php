<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class CamelConverter
{
    public static function convert(array $data): array
    {
        $camelJson = static function (array $array) use (&$camelJson) {
            $replaced = [];
            foreach ($array as $key => $value) {
                $key = Str::camel($key);
                if (is_array($value)) {
                    $replaced[$key] = $camelJson($value);
                } else {
                    $replaced[$key] = $value;
                }
            }

            return $replaced;
        };

        return $camelJson($data);
    }
}
