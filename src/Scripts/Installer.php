<?php

namespace Kyojin\JWT\Scripts;

/**
 * Installer Class for JWT Package
 * 
 * This class handles automatic configuration of JWT environment variables
 * during package installation or update.
 * 
 * @package Kyojin\JWT\Scripts
 * @version 1.0.1
 */
class Installer
{
    /**
     * Post-installation script to add JWT environment variables
     * 
     * This method is called by Composer after package installation/update.
     * It attempts to locate and modify the .env file with JWT-specific variables.
     * 
     * @return void
     */
    public static function postInstall()
    {
        // calling a private method to get the env path
        $envPath = self::getEnv();
        
        // check if the env file exists
        if (!$envPath) {
            echo "Could not locate .env file. Please manually add JWT environment variables.\n";
            return;
        }

        // check env file permissions
        if (!is_writable($envPath)) {
            echo ".env file is not writable. Please add JWT variables manually.\n";
            return;
        }

        // call the private method to apply modifications to .env file
        self::applyEnv($envPath);
    }

    /**
     * Locate the .env file
     * 
     * Searches for the .env file in project directory locations.
     * 
     * @return string|null Path to the .env file, or null if not found
     */
    private static function getEnv()
    {
        // possible locations for .env file
        $possiblePaths = [
            getcwd() . '/.env',           // current working directory
            dirname(getcwd()) . '/.env',  // parent directory
        ];

        // loop through possible paths
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Apply JWT-specific environment variables to the .env file
     * 
     * Adds missing JWT configuration variables to the .env file.
     * 
     * @param string $path Path to the .env file
     * @return void
     */
    private static function applyEnv($path)
    {
        // read existing .env file content
        $content = file_get_contents($path);
        $lines = explode("\n", $content);
        
        // default JWT configuration variables
        $vars = [
            'JWT_SECRET' => bin2hex(random_bytes(32)),  // secure random secret using bin2hex
            'JWT_ALGO' => 'HS256',                      // default algorithm
            'JWT_TTL' => 60                             // token time-to-live in minutes
        ];

        $modified = false;

        // check and add missing env variables
        foreach ($vars as $key => $val) {
            if (!self::checkExists($lines, $key)) {
                $lines[] = "$key=$val";
                echo "Added $key to .env\n";
                $modified = true;
            }
        }
        
        // apply midifications to the .env file
        if ($modified) {
            // removing empty lines
            $lines = array_filter($lines);
            $content = implode("\n", $lines) . "\n";
            
            file_put_contents($path, $content);
        }
    }

    /**
     * Check if a specific environment variable already exists in the .env file
     * 
     * @param array  $lines Array of .env file lines
     * @param string $key   Environment variable key to check
     * @return bool True if the variable exists, false otherwise
     */
    private static function checkExists(array $lines, string $key)
    {
        foreach ($lines as $line) {
            // trim whitespaces and look for a match
            $trimmedLine = trim($line);
            if (strpos($trimmedLine, $key . '=') === 0) {
                return true;
            }
        }
        return false;
    }
}