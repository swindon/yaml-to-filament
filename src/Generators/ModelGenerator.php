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
            if (isset($definition['fillable']) && is_array($definition['fillable'])) {
                $fillable = implode("', '", $definition['fillable']);
            }

            $variables = [
                'MODEL_NAME' => $modelName,
                'FILLABLE'   => $fillable,
            ];

            $rendered = $this->renderStub($stubPath, $variables);

            $outputPath = config('filament-yaml-generator.output_paths.models') . '/' . $modelName . '.php';
            $this->writeFile($outputPath, $rendered);
        }
    }
}
