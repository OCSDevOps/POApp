<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class SupplierAuthenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('supplier.login');
        }
    }

    /**
     * Specify the guard to be used.
     */
    protected function authenticate($request, array $guards)
    {
        parent::authenticate($request, ['supplier']);
    }
}