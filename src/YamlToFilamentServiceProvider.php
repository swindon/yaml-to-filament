<?php

namespace Swindon\YamlToFilament;

use Illuminate\Support\ServiceProvider;
use Swindon\YamlToFilament\Commands\GenerateFromYamlCommand;

class YamlToFilamentServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Merge package config with user config
        $this->mergeConfigFrom(__DIR__ . '/../config/filament-yaml-generator.php', 'filament-yaml-generator');
        
        // Register command
        $this->app->singleton('command.filament.generate.yaml', function ($app) {
            return new GenerateFromYamlCommand();
        });
        $this->commands(['command.filament.generate.yaml']);
    }
    
    public function boot()
    {
        // Publish configuration file for customization.
        $this->publishes([
            __DIR__ . '/../config/filament-yaml-generator.php' => config_path('filament-yaml-generator.php'),
        ], 'config');

        if (!file_exists(config_path('filament-yaml-generator.php'))) {
            throw new \RuntimeException("Configuration file not published. Run 'php artisan vendor:publish'.");
        }
    }
}
