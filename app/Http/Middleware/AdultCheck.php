<?php

namespace App\Http\Middleware;

use App\Models\Group;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdultCheck
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $group = $request->route('group');

        if ($group->age_verify) {
            $user = Auth::user();

            if(!$user){
                abort(403);
            }

            if ($user->birthday == null) {
                abort(403);
            }

            if ($user && !$user->isAdult()) {
                abort(403);
            }
        }

        return $next($request);
    }
}
