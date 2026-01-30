<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SetTenantContext
{
    /**
     * Handle an incoming request and set the tenant (company) context.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $guards = [
            'web' => auth(),
            'supplier' => auth('supplier'),
        ];

        foreach ($guards as $guardName => $guard) {
            if ($guard->check()) {
                $user = $guard->user();

                if ($user && isset($user->company_id) && $user->company_id && !session()->has('company_id')) {
                    session(['company_id' => $user->company_id]);

                    Log::info('Tenant context set', [
                        'guard' => $guardName,
                        'user_id' => $user->getAuthIdentifier(),
                        'company_id' => $user->company_id
                    ]);
                }

                // Stop after the first authenticated guard
                break;
            }
        }

        return $next($request);
    }
}
