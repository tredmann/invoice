<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ConvertRequestFieldsToSnakeCase
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $snakeJson = static function (array $array) use (&$snakeJson) {
            $replaced = [];
            foreach ($array as $key => $value) {
                $key = Str::snake($key);
                if (is_array($value)) {
                    $replaced[$key] = $snakeJson($value);
                } else {
                    $replaced[$key] = $value;
                }
            }

            return $replaced;
        };

        $replaced = $snakeJson($request->all());
        $request->replace($replaced);

        return $next($request);
    }
}
