<?php

namespace App\Http\Middleware;

use App\Support\OfficeContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveOfficeContext
{
    public function __construct(
        private readonly OfficeContext $officeContext,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $this->officeContext->resolve($request->user());
        }

        return $next($request);
    }
}
