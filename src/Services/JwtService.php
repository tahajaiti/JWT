<?php

namespace Kyojin\JWT\Services;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

/**
 * JwtService - JSON Web Token Authentication Service
 * 
 * Handles JWT token generation, decoding, validation, and user retrieval
 * 
 * @package Kyojin\JWT\Services
 * @version 1.0.0
 */
class JwtService
{
    /**
     * JWT Secret Key
     * @var string
     */
    protected string $secret;

    /**
     * Encryption Algorithm
     * @var string
     */
    protected string $algo;

    /**
     * Decoded Token Payload
     * @var object|null
     */
    protected $decoded;

    /**
     * User Model Class
     * @var string
     */
    protected string $userModel;

    /**
     * Constructor
     * 
     * Initializes JWT configuration from environment and config files
     */
    public function __construct()
    {
        $this->secret = env('JWT_SECRET', Config::get('jwt.secret'));
        $this->algo = env('JWT_ALGO', Config::get('jwt.algo', 'HS256'));
        $this->userModel = Config::get('jwt.user_model', 'App\Models\User');
    }

    /**
     * Generate a JWT token for a given user
     * 
     * @param mixed $user User model instance
     * @return string Generated JWT token
     */
    public function generate($user)
    {
        $payload = $this->payload($user);
        return JWT::encode($payload, $this->secret, $this->algo);
    }

    /**
     * Create token payload
     * 
     * @param mixed $user User model instance
     * @return array Token payload
     */
    private function payload($user)
    {
        return [
            'iss' => Config::get('app.name'),  // Issuer
            'sub' => $user->id,                // Subject (user ID)
            'iat' => Carbon::now()->timestamp, // Issued At
            'exp' => Carbon::now()->addHours(6)->timestamp, // Expiration Time
        ];
    }

    /**
     * Decode JWT token
     * 
     * @param string $token JWT token
     * @return object|null Decoded token payload or null if invalid
     */
    public function decode(string $token)
    {
        try {
            return JWT::decode($token, new Key($this->secret, $this->algo));
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Validate JWT token
     * 
     * @param string $token JWT token
     * @return bool True if token is valid
     * @throws Exception If token or user is invalid
     */
    public function validate(string $token)
    {
        $decode = $this->decode($token);
        $this->decoded = $decode;
        $user = $this->user();

        if (!$decode) {
            throw new Exception('Invalid token');
        }

        if (!$user) {
            throw new Exception('Invalid user');
        }

        return true;
    }

    /**
     * Retrieve authenticated user
     * 
     * @return mixed User model instance
     * @throws Exception If user cannot be retrieved
     */
    public function user()
    {
        if (!$this->decoded || !isset($this->decoded->sub)) {
            throw new Exception('Invalid user id');
        }

        $model = $this->userModel;
        return $model::where('id', $this->decoded->sub)->first();
    }
}