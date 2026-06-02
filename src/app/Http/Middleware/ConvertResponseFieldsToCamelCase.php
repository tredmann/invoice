<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ConvertResponseFieldsToCamelCase
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $content = $response->getContent();
        $json = json_decode((string) $content, true);

        if (! is_array($json)) {
            return $response;
        }

        $camelJson = static function (array $array, $root = null) use (&$camelJson): array {
            // there might be a better approach
            $replaced = [];
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $replaced[Str::camel($key)] = $camelJson($value, $root = $key);
                } elseif ($value === null) {
                    $replaced[Str::camel($key)] = $value; // maybe consider 'continue'
                } else {
                    $replaced[Str::camel($key)] = str_replace([
                        $root, str_replace('_', ' ', $root),
                    ], Str::camel($root), $value);
                }
            }

            return $replaced;
        };
        $response->setContent(json_encode($camelJson($json)));

        return $response;
    }
}
