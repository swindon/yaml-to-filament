<?php

namespace Swindon\YamlToFilament\Generators;

class PageGenerator extends BaseGenerator
{
    /**
     * Generate Filament resource pages for each resource defined in the blueprint.
     *
     * Expected blueprint structure:
     *
     * resources:
     *   Product:
     *     # (Optional custom page settings can be defined here)
     *
     * For each resource, this generator creates:
     * - List{ResourceName}s.php (List page)
     * - Create{ResourceName}.php (Create page)
     * - Edit{ResourceName}.php (Edit page)
     *
     * @param array $blueprintData
     */
    public function generate(array $blueprintData)
    {
        if (!isset($blueprintData['resources']) || !is_array($blueprintData['resources'])) {
            return;
        }

        foreach ($blueprintData['resources'] as $resourceName => $definition) {
            $this->generatePagesForResource($resourceName);
        }
    }

    /**
     * Generate all pages for a specific resource.
     *
     * @param string $resourceName
     */
    protected function generatePagesForResource(string $resourceName)
    {
        // Retrieve the base output path for resources from configuration.
        $baseOutputPath = config('filament-yaml-generator.output_paths.resources');
        $pagesDir = rtrim($baseOutputPath, '/') . '/' . $resourceName . '/Pages';

        if (!is_dir($pagesDir)) {
            mkdir($pagesDir, 0755, true);
        }

        // Generate the List page.
        $listPageContent = $this->getListPageContent($resourceName);
        $listPageFile = $pagesDir . '/List' . $resourceName . 's.php';
        $this->writeFile($listPageFile, $listPageContent);

        // Generate the Create page.
        $createPageContent = $this->getCreatePageContent($resourceName);
        $createPageFile = $pagesDir . '/Create' . $resourceName . '.php';
        $this->writeFile($createPageFile, $createPageContent);

        // Generate the Edit page.
        $editPageContent = $this->getEditPageContent($resourceName);
        $editPageFile = $pagesDir . '/Edit' . $resourceName . '.php';
        $this->writeFile($editPageFile, $editPageContent);
    }

    /**
     * Get the content for the List page.
     *
     * @param string $resourceName
     * @return string
     */
    protected function getListPageContent(string $resourceName): string
    {
        return <<<PHP
<?php

namespace App\Filament\Resources\\{$resourceName}\Pages;

use App\Filament\Resources\\{$resourceName}Resource;
use Filament\Resources\Pages\ListRecords;

class List{$resourceName}s extends ListRecords
{
    protected static string \$resource = {$resourceName}Resource::class;
}
PHP;
    }

    /**
     * Get the content for the Create page.
     *
     * @param string $resourceName
     * @return string
     */
    protected function getCreatePageContent(string $resourceName): string
    {
        return <<<PHP
<?php

namespace App\Filament\Resources\\{$resourceName}\Pages;

use App\Filament\Resources\\{$resourceName}Resource;
use Filament\Resources\Pages\CreateRecord;

class Create{$resourceName} extends CreateRecord
{
    protected static string \$resource = {$resourceName}Resource::class;
}
PHP;
    }

    /**
     * Get the content for the Edit page.
     *
     * @param string $resourceName
     * @return string
     */
    protected function getEditPageContent(string $resourceName): string
    {
        return <<<PHP
<?php

namespace App\Filament\Resources\\{$resourceName}\Pages;

use App\Filament\Resources\\{$resourceName}Resource;
use Filament\Resources\Pages\EditRecord;

class Edit{$resourceName} extends EditRecord
{
    protected static string \$resource = {$resourceName}Resource::class;
}
PHP;
    }
}
