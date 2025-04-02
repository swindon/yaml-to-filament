<?php

namespace Swindon\YamlToFilament\Generators;

class WidgetGenerator extends BaseGenerator
{
    /**
     * Generate Filament widget classes from blueprint data.
     *
     * Expected blueprint structure:
     *
     * widgets:
     *   SalesOverview:
     *     title: "Sales Overview"
     *     view: "filament.widgets.sales-overview"
     *     properties:
     *       refreshInterval: 30
     *       chartType: "line"
     *
     * If 'title' is not provided, the widget name is used.
     * If 'view' is not provided, a default view path is generated.
     *
     * @param array $blueprintData
     */
    public function generate(array $blueprintData)
    {
        if (!isset($blueprintData['widgets']) || !is_array($blueprintData['widgets'])) {
            return;
        }

        // Determine the output path for widgets from config or default to app/Filament/Widgets.
        $outputPath = config('filament-yaml-generator.output_paths.widgets', app_path('Filament/Widgets'));
        if (!is_dir($outputPath)) {
            mkdir($outputPath, 0755, true);
        }

        foreach ($blueprintData['widgets'] as $widgetName => $definition) {
            $title = isset($definition['title']) ? addslashes($definition['title']) : $widgetName;
            $view = isset($definition['view'])
                ? $definition['view']
                : 'filament.widgets.' . strtolower($widgetName);

            // Process additional properties if defined.
            $additionalPropertiesCode = '';
            if (isset($definition['properties']) && is_array($definition['properties'])) {
                foreach ($definition['properties'] as $propKey => $propValue) {
                    $exportedValue = var_export($propValue, true);
                    $additionalPropertiesCode .= "    protected static \${$propKey} = {$exportedValue};\n\n";
                }
            }

            $widgetContent = <<<PHP
<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class {$widgetName} extends Widget
{
    protected static ?string \$heading = '{$title}';

    protected static string \$view = '{$view}';

{$additionalPropertiesCode}    // Additional widget logic can be added here.
}
PHP;

            $fileName = $outputPath . '/' . $widgetName . '.php';
            $this->writeFile($fileName, $widgetContent);
        }
    }
}
