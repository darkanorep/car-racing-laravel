<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ModeratorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $id = auth()->user()->id;
        $user = User::where('id', $id)->first();
        $role = $user->role;

        if ($role == 'moderator') {

        } else {
            return response()->json([
                'message' => 'Unauthorized Access.'
            ]);
        }

        return $next($request);
    }
}
