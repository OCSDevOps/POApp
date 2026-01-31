<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SetTenantContext
{
    /**
     * Handle an incoming request and set the tenant (company) context.
     * 
     * Establishes company context for tenant isolation. This middleware ensures
     * every authenticated request has a company_id in session, which is used by
     * the CompanyScope trait to filter all database queries.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $guards = [
            'web' => auth(),
            'supplier' => auth('supplier'),
        ];

        foreach ($guards as $guardName => $guard) {
            if ($guard->check()) {
                $user = $guard->user();

                if ($user && isset($user->company_id) && $user->company_id) {
                    // Set or update session with company context
                    if (!session()->has('company_id') || session('company_id') != $user->company_id) {
                        // Get company name for display (with caching to avoid extra queries)
                        $companyName = 'Unknown Company';
                        if ($user->relationLoaded('company') && $user->company) {
                            $companyName = $user->company->name;
                        } elseif (method_exists($user, 'company')) {
                            $company = $user->company;
                            $companyName = $company ? $company->name : 'Unknown Company';
                        }

                        session([
                            'company_id' => $user->company_id,
                            'company_name' => $companyName,
                        ]);

                        Log::info('Tenant context set', [
                            'guard' => $guardName,
                            'user_id' => $user->getAuthIdentifier(),
                            'company_id' => $user->company_id,
                            'company_name' => $companyName,
                        ]);
                    }

                    // Make company_id available in request for easy access
                    $request->merge(['tenant_company_id' => session('company_id')]);

                    // Share company info with all views
                    view()->share('current_company_id', session('company_id'));
                    view()->share('current_company_name', session('company_name', 'Unknown'));
                } else {
                    // User has no company_id assigned
                    if ($guardName === 'web' && isset($user->u_type) && $user->u_type != 1) {
                        // Regular users (not super admin) MUST have a company assigned
                        Log::warning('User without company attempted access', [
                            'guard' => $guardName,
                            'user_id' => $user->getAuthIdentifier(),
                        ]);
                        
                        abort(403, 'User has no company assigned. Please contact administrator.');
                    }
                    // Super admins and supplier users can proceed without company context
                }

                // Stop after the first authenticated guard
                break;
            }
        }

        return $next($request);
    }
}
