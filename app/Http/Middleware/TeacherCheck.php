<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TeacherCheck
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return abort(403);
        }

        $hasTeacherRole = auth()->user()->roles->contains('slug', 'teacher');

        if ($hasTeacherRole) {
            return $next($request);
        }

        return abort(403);
    }
}
