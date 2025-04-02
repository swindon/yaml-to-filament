<?php

namespace Swindon\YamlToFilament\Commands;

use Illuminate\Console\Command;
use Swindon\YamlToFilament\Generators\MigrationGenerator;
use Swindon\YamlToFilament\Generators\ModelGenerator;
use Swindon\YamlToFilament\Generators\PageGenerator;
use Swindon\YamlToFilament\Generators\RelationshipGenerator;
use Swindon\YamlToFilament\Generators\ResourceGenerator;
use Swindon\YamlToFilament\Generators\WidgetGenerator;
use Swindon\YamlToFilament\Parser\YamlParser;
// TODO: Add other necessary imports

class GenerateFromYamlCommand extends Command
{
    protected $signature = 'filament:generate-from-yaml {--file= : The YAML file or folder to process}';
    protected $description = 'Generate Filament components from YAML definitions';
    
    public function handle()
    {
        $path = $this->option('file') ?: config('filament-yaml-generator.yaml_path', base_path('filament-yaml'));
        
        $this->info("Processing YAML files in: {$path}");
        
        $parser = new YamlParser();
        try {
            $blueprintData = $parser->parseFile($path);
        } catch (\Exception $e) {
            $this->error("Failed to parse YAML file: " . $e->getMessage());
            return Command::FAILURE;
        }
        
        $generators = [
            'Model' => new ModelGenerator(),
            'Migration' => new MigrationGenerator(),
            'Resource' => new ResourceGenerator(),
            'Page' => new PageGenerator(),
            'Widget' => new WidgetGenerator(),
            'Relationship' => new RelationshipGenerator(),
            // TODO: Add other generators as needed
        ];
        
        foreach ($generators as $name => $generator) {
            try {
                $generator->generate($blueprintData);
                $this->info("{$name} generated successfully.");
            } catch (\Exception $e) {
                $this->error("Failed to generate {$name}: " . $e->getMessage());
            }
        }
        
        $this->info("Filament components generation process completed.");
        return Command::SUCCESS;
    }
}
