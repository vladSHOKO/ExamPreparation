<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasRoleStudentChecker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isStudent = $request->user()->role === 'student';

        if(!$isStudent){
            return abort(403, 'У вас нет доступа к списку заданий!');
        }

        return $next($request);
    }
}
