<?php

namespace Kyojin\JWT\Services;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class JwtService
{
    protected string $secret;
    protected string $algo;
    protected $decoded;
    protected string $userModel;

    public function __construct()
    {
        $this->secret = env('JWT_SECRET', Config::get('jwt.secret'));
        $this->algo = env('JWT_ALGO', Config::get('jwt.algo', 'HS256'));
        $this->userModel = Config::get('jwt.user_model', 'App\Models\User');
    }

    public function generate($user)
    {
        $payload = $this->payload($user);
        return JWT::encode($payload, $this->secret, $this->algo);
    }

    private function payload($user)
    {
        return [
            'iss' => Config::get('app.name'),
            'sub' => $user->id,
            'iat' => Carbon::now()->timestamp,
            'exp' => Carbon::now()->addHours(6)->timestamp,
        ];
    }

    public function decode(string $token)
    {
        try {
            return JWT::decode($token, new Key($this->secret, $this->algo));
        } catch (Exception $e) {
            return null;
        }
    }

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

    public function user()
    {
        if (!$this->decoded || !isset($this->decoded->sub)) {
            throw new Exception('Invalid user id');
        }

        $model = $this->userModel;
        return $model::where('id', $this->decoded->sub)->first();
    }
}