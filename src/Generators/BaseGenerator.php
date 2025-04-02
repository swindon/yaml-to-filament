<?php

namespace Swindon\YamlToFilament\Generators;

abstract class BaseGenerator
{
    protected function renderStub($stubPath, array $variables)
    {
        $stub = file_get_contents($stubPath);
        foreach ($variables as $key => $value) {
            $stub = str_replace("{{ $key }}", $value, $stub);
        }
        return $stub;
    }
    
    protected function writeFile($path, $content)
    {
        // Create directory if not exists.
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        file_put_contents($path, $content);
    }
    
    // Each generator must implement a generate() method.
    abstract public function generate(array $blueprintData);
}
