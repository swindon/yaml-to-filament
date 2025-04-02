<?php

namespace Swindon\YamlToFilament\Generators;

class ResourceGenerator extends BaseGenerator
{
    /**
     * Generate Filament resource files from blueprint data.
     *
     * Expected blueprint structure for resources:
     * 
     * resources:
     *   Product:
     *     form:
     *       - field: name
     *         type: text
     *         required: true
     *         label: "Product Name"
     *       - field: price
     *         type: number
     *         required: true
     *     table:
     *       - column: name
     *         label: "Product Name"
     *       - column: price
     *         label: "Price"
     *
     * @param array $blueprintData
     */
    public function generate(array $blueprintData)
    {
        if (!isset($blueprintData['resources']) || !is_array($blueprintData['resources'])) {
            return;
        }

        foreach ($blueprintData['resources'] as $resourceName => $definition) {
            // Generate the form schema code from the blueprint's "form" section.
            $formSchemaCode = $this->generateFormSchema($definition['form'] ?? []);
            // Generate the table columns code from the blueprint's "table" section.
            $tableColumnsCode = $this->generateTableColumns($definition['table'] ?? []);

            $code = <<<PHP
<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use App\\{$resourceName};

class {$resourceName}Resource extends Resource
{
    protected static ?string \$model = {$resourceName}::class;

    public static function form(Forms\\Form \$form): Forms\\Form
    {
        return \$form
            ->schema([
{$formSchemaCode}
            ]);
    }

    public static function table(Tables\\Table \$table): Tables\\Table
    {
        return \$table
            ->columns([
{$tableColumnsCode}
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\\List{$resourceName}s::route('/'),
            'create' => Pages\\Create{$resourceName}::route('/create'),
            'edit'   => Pages\\Edit{$resourceName}::route('/{record}/edit'),
        ];
    }
}
PHP;

            $outputPath = config('filament-yaml-generator.output_paths.resources') . '/' . $resourceName . 'Resource.php';
            $this->writeFile($outputPath, $code);
        }
    }

    /**
     * Generate PHP code for the form schema.
     *
     * @param array \$formFields Array of field definitions.
     * @return string
     */
    protected function generateFormSchema(array $formFields): string
    {
        $lines = [];
        foreach ($formFields as $field) {
            // Each field is expected to be an associative array with keys:
            // 'field' (name), 'type', 'required' (boolean), and optional 'label'.
            $fieldName = $field['field'] ?? null;
            if (!\$fieldName) {
                continue;
            }
            $type = strtolower($field['type'] ?? 'text');
            $component = $this->mapFormComponent($type);
            $codeLine = "                {$component}::make('{$fieldName}')";
            if (isset($field['label'])) {
                $label = addslashes($field['label']);
                $codeLine .= "->label('{$label}')";
            }
            if (!empty($field['required']) && $field['required'] === true) {
                $codeLine .= "->required()";
            }
            if ($type === 'number') {
                $codeLine .= "->numeric()";
            }
            $codeLine .= ",";
            $lines[] = $codeLine;
        }
        return implode("\n", $lines);
    }

    /**
     * Map a YAML field type to a Filament form component.
     *
     * @param string $type
     * @return string
     */
    protected function mapFormComponent(string $type): string
    {
        $mapping = [
            'text'     => 'Forms\\Components\\TextInput',
            'number'   => 'Forms\\Components\\TextInput',
            'textarea' => 'Forms\\Components\\Textarea',
            'select'   => 'Forms\\Components\\Select',
            // Extend with more mappings as needed.
        ];

        return $mapping[$type] ?? 'Forms\\Components\\TextInput';
    }

    /**
     * Generate PHP code for the table columns.
     *
     * @param array $tableColumns Array of column definitions.
     * @return string
     */
    protected function generateTableColumns(array $tableColumns): string
    {
        $lines = [];
        foreach ($tableColumns as $column) {
            // Each column is expected to be an associative array with keys:
            // 'column' (the field name) and optional 'label'.
            $columnName = $column['column'] ?? null;
            if (!$columnName) {
                continue;
            }
            $codeLine = "                Tables\\Columns\\TextColumn::make('{$columnName}')";
            if (isset($column['label'])) {
                $label = addslashes($column['label']);
                $codeLine .= "->label('{$label}')";
            }
            $codeLine .= ",";
            $lines[] = $codeLine;
        }
        return implode("\n", $lines);
    }
}
