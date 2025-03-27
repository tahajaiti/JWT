<?php

namespace Kyojin\JWT\Http\Middleware;

use Kyojin\JWT\Facades\JWT;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Routing\ResponseFactory;
use Symfony\Component\HttpFoundation\Response;

class JwtMiddleware
{
    protected $responseFactory;

    public function __construct(ResponseFactory $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $jwt = $request->bearerToken();
        if (empty($jwt)) {
            return $this->responseFactory->json(['error' => 'No Token'], 401);
        }

        try {
            JWT::validate($jwt);
            $user = JWT::user();
            Auth::setUser($user);
        } catch (Exception $e) {
            return $this->responseFactory->json(['error' => $e->getMessage()], 401);
        }

        return $next($request);
    }
}