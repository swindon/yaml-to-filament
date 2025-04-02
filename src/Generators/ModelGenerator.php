<?php

namespace Swindon\YamlToFilament\Generators;

class ModelGenerator extends BaseGenerator
{
    public function generate(array $blueprintData)
    {
        if (!isset($blueprintData['models']) || !is_array($blueprintData['models'])) {
            return;
        }

        foreach ($blueprintData['models'] as $modelName => $definition) {
            $stubPath = config('filament-yaml-generator.stubs_path') . '/model.stub';

            // Process "fillable" if defined (assumes an array)
            $fillable = '';
            if (isset($definition['fillable'])) {
                if (!is_array($definition['fillable'])) {
                    throw new \InvalidArgumentException("The 'fillable' key for model '{$modelName}' must be an array.");
                }
                $fillable = implode("', '", $definition['fillable']);
            }

            $variables = [
                'MODEL_NAME' => $modelName,
                'FILLABLE'   => $fillable,
            ];

            $rendered = $this->renderStub($stubPath, $variables);

            $outputPath = config('filament-yaml-generator.output_paths.models') . '/' . $modelName . '.php';
            $this->writeFile($outputPath, $rendered);

            // Add error handling
            if (!file_exists($outputPath)) {
                throw new \RuntimeException("Failed to write model file: {$outputPath}");
            }
        }
    }
}
