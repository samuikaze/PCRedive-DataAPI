<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\BearerTokenService;
use App\Services\ResponseService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyAuthentication
{
    /**
     * 回應
     * 
     * @var \App\Services\ResponseService
     */
    protected $response;

    /**
     * 權杖
     * 
     * @var \App\Services\BearerTokenService
     */
    protected $token;

    /**
     * 建構函式
     * 
     * @return void
     */
    public function __construct(
        BearerTokenService $token,
        ResponseService $response
    ) {
        $this->response = $response;
        $this->token = $token;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 如果是 API
        if ($request->wantsJson() && $request->isJson()) {
            $token = $request->bearerToken();

            if (empty($token)) {
                return $this->response->setError('Access Denied')->setCode($this->response::FORBIDDEN)->json();
            }
        
            $verify = $this->token->verifyToken($token);

            if ($verify === false) {
                return $this->response->setError('Unauthorized')->setCode($this->response::UNAUTHORIZED)->json();
            }

            $user = User::where('id', $verify)->first()->toArray();

            $request->merge(['user' => $user]);

            return $next($request);
        }
        // 如果是瀏覽器
        else {
            if (!Auth::check()) {
                return $this->response->setRedirectTarget(route('login'))->redirect();
            }

            return $next($request);
        }
    }
}
