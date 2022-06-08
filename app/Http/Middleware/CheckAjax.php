<?php

namespace App\Http\Middleware;

use App\Config\AppConfig;
use Closure;

class CheckAjax
{
    /**
     * Handle an incoming request.
     * Определение в конфиге типа запроса
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->format() === 'json')
            AppConfig::set('isAjax', true);
        else
            AppConfig::set('isAjax', false);
        return $next($request);
    }
}
