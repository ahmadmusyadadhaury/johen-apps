<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StaffHrAccess
{
    private const BLOCKED_ROUTE_PREFIXES = [
        'meeting.', 'assets.', 'digital-registries.', 'electricity.', 'internet.',
        'digital.', 'ipl.', 'reimbursement', 'hris.weekly-meeting.',
        'api.meetings', 'api.assets', 'api.asset-categories', 'api.digital-assets',
        'api.payment-categories.', 'api.payments.', 'api.electricity.', 'api.internet.', 'api.ipl.',
    ];

    private const BLOCKED_PATH_PREFIXES = [
        'api/meetings', 'api/meeting-requests', 'api/assets', 'api/asset-categories',
        'api/digital-assets', 'api/payments', 'api/payment-categories', 'api/electricity',
        'api/internet', 'api/ipl', 'api/payment-submissions',
    ];

    private const READ_ONLY_PATH_PREFIXES = [
        'hris/', 'payroll/', 'history/', 'bonus/', 'electricity/', 'internet/',
        'digital/', 'ipl/', 'assets/', 'digital-registries/', 'reimbursement',
        'api/meetings', 'api/assets', 'api/asset-categories', 'api/digital-assets',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user?->isStaffHr()) {
            return $next($request);
        }

        $routeName = $request->route()?->getName() ?? '';
        foreach (self::BLOCKED_ROUTE_PREFIXES as $prefix) {
            if (str_starts_with($routeName, $prefix)) {
                abort(403, 'Anda tidak memiliki akses ke menu ini.');
            }
        }
        $path = ltrim($request->path(), '/');
        foreach (self::BLOCKED_PATH_PREFIXES as $prefix) {
            if (str_starts_with($path, $prefix)) {
                abort(403, 'Anda tidak memiliki akses ke menu ini.');
            }
        }

        if ($request->is('livewire/update')) {
            $this->denyLivewireWrites($request);
        }

        if ($this->isReadOnlyPath($request) && ! in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            abort(403, 'Staff HR hanya memiliki akses baca pada menu ini.');
        }

        return $next($request);
    }

    private function isReadOnlyPath(Request $request): bool
    {
        foreach (self::READ_ONLY_PATH_PREFIXES as $prefix) {
            if ($request->is($prefix) || $request->is($prefix.'*')) {
                return true;
            }
        }

        return false;
    }

    private function denyLivewireWrites(Request $request): void
    {
        $writeAction = '/^(?:create|store|save|update|delete|destroy|remove|approve|reject|edit|confirmDelete|mark|upload|submit|sync|generate|archive|restore|cancel|return|set|toggle|assign|change|feedback)/i';

        foreach ((array) $request->input('components', []) as $component) {
            $path = ltrim((string) data_get($component, 'snapshot.memo.path', ''), '/');
            $isReadOnlyArea = false;
            foreach (self::READ_ONLY_PATH_PREFIXES as $prefix) {
                if (str_starts_with($path, $prefix)) {
                    $isReadOnlyArea = true;
                    break;
                }
            }

            if (! $isReadOnlyArea) {
                continue;
            }

            foreach ((array) data_get($component, 'calls', []) as $call) {
                if (preg_match($writeAction, (string) data_get($call, 'method', ''))) {
                    abort(403, 'Staff HR hanya memiliki akses baca pada menu ini.');
                }
            }
        }
    }
}
