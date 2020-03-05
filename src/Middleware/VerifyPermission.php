<?php declare(strict_types=1);

namespace Zablose\Rap\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Zablose\Rap\Exceptions\PermissionDeniedException;

class VerifyPermission
{
    protected Guard $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @param  string   $permission
     *
     * @return mixed
     *
     * @throws PermissionDeniedException
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        if ($this->auth->check() && $this->auth->user()->rap()->can([$permission])) {
            return $next($request);
        }

        throw new PermissionDeniedException($permission);
    }
}
