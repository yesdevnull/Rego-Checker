<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Symfony\Component\Security\Core\Util\StringUtils;

class VerifyCsrfToken extends BaseVerifier {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		return parent::handle($request, $next);
	}

    protected function tokensMatch($request)
    {
        $token = $request->session()->token();

        $header = $request->header('X-XSRF-TOKEN');

        return StringUtils::equals($token, $request->input('_token')) ||
               ($header && StringUtils::equals($token, $header));
    }

}
