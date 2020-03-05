<?php declare(strict_types=1);

namespace Zablose\Rap\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Zablose\Rap\Exceptions\RoleDeniedException;

class VerifyRole
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
     * @param  string   $role
     *
     * @return mixed
     *
     * @throws RoleDeniedException
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        if ($this->auth->check() && $this->auth->user()->rap()->is([$role])) {
            return $next($request);
        }

        throw new RoleDeniedException($role);
    }
}
