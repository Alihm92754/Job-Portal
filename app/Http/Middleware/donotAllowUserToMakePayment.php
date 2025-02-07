<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class donotAllowUserToMakePayment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
    
        if (!$user) {
            return redirect()->route('login'); // Ensure user is authenticated
        }
    
        if (!empty($user->billing_ends) && $user->billing_ends > date('Y-m-d')) {
            return redirect()->route('dashboard')->with('error', 'You are a paid member');
        }
    
        return $next($request);
    }

}
