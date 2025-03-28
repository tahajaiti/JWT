<?php

namespace Kyojin\JWT\Http\Middleware;

use Kyojin\JWT\Facades\JWT;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Routing\ResponseFactory;
use Symfony\Component\HttpFoundation\Response;

/**
 * JwtMiddleware for Authentication and Authorization
 * 
 * Handles JWT token validation and user authentication in HTTP requests
 * 
 * @package Kyojin\JWT\Http\Middleware
 * @version 1.0.0
 */
class JwtMiddleware
{
    /**
     * Response Factory for creating standardized API responses
     * 
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * Constructor for JwtMiddleware
     * 
     * Dependency injection of ResponseFactory for consistent error handling
     * 
     * @param ResponseFactory $responseFactory
     */
    public function __construct(ResponseFactory $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * Handle incoming request
     * 
     * Validates JWT token and authenticates user
     * 
     * @param Request $request Incoming HTTP request
     * @param Closure $next Closure to pass request to next middleware
     * @return Response HTTP response
     * 
     * @throws Exception If token validation fails
     */
    public function handle(Request $request, Closure $next): Response
    {
        // get bearer token from request header
        $jwt = $request->bearerToken();
        
        // check if token is missing
        if (empty($jwt)) {
            return $this->responseFactory->json([
                'error' => 'No Token',
                'message' => 'Authorization token is required'
            ], 401);
        }

        try {
            // attempt to validate token
            JWT::validate($jwt);
            
            // get the user from the helper method
            $user = JWT::user();
            
            // set the user into the auth facade
            Auth::setUser($user);
        } catch (Exception $e) {
            return $this->responseFactory->json([
                'error' => 'Authentication Failed',
                'message' => $e->getMessage()
            ], 401);
        }

        return $next($request);
    }
}