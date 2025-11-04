<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Determine if the request has a URI that should pass through CSRF verification.
     */
    protected function inExceptArray($request): bool
    {
        if ($this->app->runningUnitTests() || $this->app->environment('testing')) {
            return true;
        }

        return parent::inExceptArray($request);
    }
}
