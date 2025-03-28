<?php

namespace Kyojin\JWT\Scripts;

class ProviderInstaller
{
    /**
     * Path to the configuration file
     * @var string
     */
    private static $configPath;

    /**
     * Fully qualified class name of the JWT service provider
     * @var string
     */
    private static $providerClass = 'Kyojin\\JWT\\Providers\\JwtServiceProvider::class';

    /**
     * Post-installation method to add JWT service provider
     * 
     * @return void
     */
    public static function postInstall()
    {
        // try to get the config file
        self::findConfigFile();

        // if no config file found, exit
        if (!self::$configPath) {
            echo "Could not locate app configuration file. Please manually add JWT service provider.\n";
            return;
        }

        // add provider to the configuration
        self::addProviderToConfig();
    }

    /**
     * Find the configuration file
     * 
     * Searches for the configuration file in common Laravel project locations
     */
    private static function findConfigFile()
    {
        $possiblePaths = [
            getcwd() . '/bootstrap/providers.php',
            dirname(getcwd()) . '/bootstrap/providers.php',
            getcwd() . '/laravel/bootstrap/providers.php',
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                self::$configPath = $path;
                return;
            }
        }
    }

    /**
     * Add JWT service provider to the configuration file
     */
    private static function addProviderToConfig()
    {
        // read the current content of the config file
        $content = file_get_contents(self::$configPath);

        // check if the provider is already registered
        if (strpos($content, self::$providerClass) !== false) {
            echo "JWT Service Provider already registered.\n";
            return;
        }

        // find the return array
        $pattern = "/(return\s*\[)([^]]*)/s";
                
        // peplace the return array, adding the new provider
        $updatedContent = preg_replace_callback(
            $pattern, 
            function($matches) {
                // trim existing content and add new provider
                $providers = rtrim($matches[2], " \t\n\r\0\x0B,");
                return $matches[1] . $providers . ",\n        " . self::$providerClass . "\n    ";
            }, 
            $content
        );

        // write the updated content back to the file
        file_put_contents(self::$configPath, $updatedContent);

        echo "Added JWT Service Provider to configuration.\n";
    }
}