<?php

namespace Kyojin\JWT\Scripts;


class Installer
{



    public static function postInstall()
    {
        $envPath = self::getEnv();
        if (!$envPath) {
            echo "Could not locate .env file. Please manually add JWT environment variables.\n";
            return;
        }

        self::applyEnv($envPath);
    }



    private static function getEnv()
    {

        $workDir = getcwd();
        $envPath = $workDir . "/.env";

        return file_exists($envPath) ? $envPath : null;
    }

    private static function applyEnv($path){
        $content = file_get_contents($path);
        $lines = explode("\n", $content);
        
        $vars = [
            'JWT_SECRET' => bin2hex(random_bytes(32)),
            'JWT_ALGO' => 'HS256',
            'JWT_TTL' => 60
        ];

        foreach ($vars as $key => $val) {
            if (!self::checkExists($lines, $key)) {
                $envLines[] = "$key=$val";
                echo "Added $key to .env\n";
            }
        }
        
        file_put_contents($path, implode("\n", array_filter($lines)));
    }

    private static function checkExists(array $lines, string $key){
        foreach ($lines as $line) {
            if (preg_match("/^$key=/", trim($line))) {
                return true;
            }
        }
        return false;
    }

}   