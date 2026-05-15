<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileIsComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Check required fields
        $requiredFields = [
            'first_name',
            'last_name',
            'phone_no',
            'address',
        ];

        foreach ($requiredFields as $field) {
            if (empty($user->$field)) {
                return response()->json([
                    'message' => 'Profile incomplete.',
                    'redirect' => '/complete-profile',
                    'missing_field' => $field,
                ], 403);
            }
        }

        if ($user->roles->isEmpty()) {
            return response()->json([
                'message' => 'User has no assigned role.',
                'redirect' => '/complete-profile',
            ], 403);
        }

        return $next($request);
    }
}
