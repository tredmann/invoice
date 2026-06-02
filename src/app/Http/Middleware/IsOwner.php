<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class IsOwner
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $modelName)
    {
        $route = $request->route();
        $model = is_object($route) ? $route->parameter($modelName) : null;

        if ($model instanceof Model) {
            if ($request->user()->id === $model->getAttribute('user_id')) {
                return $next($request);
            }
            abort(403);
        }

        return $next($request);
    }
}
