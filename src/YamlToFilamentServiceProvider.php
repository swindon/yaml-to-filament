<?php

namespace Swindon\YamlToFilament;

use Illuminate\Support\ServiceProvider;
use Swindon\YamlToFilament\Commands\GenerateFromYamlCommand;

class YamlToFilamentServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Merge package config with user config
        $this->mergeConfigFrom(__DIR__ . '/../config/yaml-to-filament.php', 'yaml-to-filament');
        
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
            __DIR__ . '/../config/yaml-to-filament.php' => config_path('yaml-to-filament.php'),
        ], 'config');
    }
}
