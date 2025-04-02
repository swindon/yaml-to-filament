<?php

namespace Swindon\YamlToFilament\Generators;

class MigrationGenerator extends BaseGenerator
{
    public function generate(array $blueprintData)
    {
        if (!isset($blueprintData['models']) || !is_array($blueprintData['models'])) {
            return;
        }

        foreach ($blueprintData['models'] as $tableName => $definition) {
            // Use the current timestamp for the migration file name.
            $timestamp = date('Y_m_d_His');
            $fileName = $timestamp . '_create_' . strtolower($tableName) . '_table.php';
            $stubPath = config('filament-yaml-generator.stubs_path') . '/migration.stub';

            // Generate column definitions. If none provided, default to id and timestamps.
            $columns = "";
            if (isset($definition['columns']) && is_array($definition['columns'])) {
                foreach ($definition['columns'] as $column => $type) {
                    $columns .= "\$table->$type('$column');\n            ";
                }
            } else {
                $columns = "\$table->id();\n            \$table->timestamps();";
            }

            $variables = [
                'TABLE_NAME' => strtolower($tableName),
                'COLUMNS'    => $columns,
            ];

            $rendered = $this->renderStub($stubPath, $variables);

            $outputPath = config('filament-yaml-generator.output_paths.migrations') . '/' . $fileName;
            $this->writeFile($outputPath, $rendered);
        }
    }
}
