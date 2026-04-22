<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $roleMap = [
            'trainer' => 1,
            'supervisor' => 2,
        ];

        foreach ($roles as $role) {
            if (isset($roleMap[$role]) && $user->role === $roleMap[$role]) {
                return $next($request);
            }
        }
        
        // 責任者は常に全画面アクセス可能
        if ($user->isSupervisor()) {
            return $next($request);
        }
        abort(403, 'この操作を行う権限はありません。');
    }
}
