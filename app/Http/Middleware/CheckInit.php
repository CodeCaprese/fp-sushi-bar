<?php

namespace App\Http\Middleware;

use App\Http\FPLib\Settings;
use Closure;
use Illuminate\Http\Request;

class CheckInit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(Settings::amountOfSeats() <= 0){
            return redirect(route("home.index"))->with("error", trans("home.error_no_init"));
        }

        return $next($request);
    }
}
