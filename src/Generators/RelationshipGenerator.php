<?php

namespace Swindon\YamlToFilament\Generators;

use Illuminate\Support\Str;

class RelationshipGenerator extends BaseGenerator
{
    /**
     * Generate relationship trait files for models defined in the blueprint.
     *
     * Expected blueprint structure:
     *
     * models:
     *   Post:
     *     relationships:
     *       belongsTo:
     *         - User
     *       hasMany:
     *         - Comment
     *         - Tag
     *       belongsToMany:
     *         - Category
     *
     * @param array $blueprintData
     */
    public function generate(array $blueprintData)
    {
        if (!isset($blueprintData['models']) || !is_array($blueprintData['models'])) {
            return;
        }

        foreach ($blueprintData['models'] as $modelName => $definition) {
            if (!isset($definition['relationships']) || !is_array($definition['relationships'])) {
                continue;
            }

            $methodsCode = $this->generateRelationshipMethods($definition['relationships']);

            // If no relationship methods were generated, skip.
            if (empty($methodsCode)) {
                continue;
            }

            // Define trait name and namespace.
            $traitName = $modelName . 'RelationshipsTrait';
            $namespace = 'App\Models\Relationships';

            // Build the trait file content.
            $traitContent = <<<PHP
<?php

namespace {$namespace};

trait {$traitName}
{
{$methodsCode}
}
PHP;

            // Determine the output directory (models output path + '/Relationships').
            $baseOutputPath = config('filament-yaml-generator.output_paths.models');
            $relationshipsPath = rtrim($baseOutputPath, '/') . '/Relationships';
            if (!is_dir($relationshipsPath)) {
                mkdir($relationshipsPath, 0755, true);
            }
            $outputFile = $relationshipsPath . '/' . $traitName . '.php';

            $this->writeFile($outputFile, $traitContent);
        }
    }

    /**
     * Generate relationship methods code from a relationships array.
     *
     * @param array $relationships
     * @return string Generated PHP code for methods.
     */
    protected function generateRelationshipMethods(array $relationships): string
    {
        $methods = [];

        // Mapping of relationship types to their Filament/Eloquent method names.
        $relationMap = [
            'belongsTo'      => 'belongsTo',
            'hasMany'        => 'hasMany',
            'hasOne'         => 'hasOne',
            'belongsToMany'  => 'belongsToMany',
        ];

        foreach ($relationships as $relationType => $relatedModels) {
            if (!isset($relationMap[$relationType])) {
                continue;
            }

            // Ensure relatedModels is an array.
            if (!is_array($relatedModels)) {
                $relatedModels = [$relatedModels];
            }

            foreach ($relatedModels as $relatedModel) {
                // Clean up model name.
                $relatedModel = trim($relatedModel);
                if (empty($relatedModel)) {
                    continue;
                }
                $relationMethod = $this->generateRelationMethodName($relationType, $relatedModel);
                $eloquentMethod = $relationMap[$relationType];
                // Fully qualified model class (assuming models are in the App namespace).
                $relatedModelClass = '\\App\\' . $relatedModel;

                $methods[] = $this->buildRelationshipMethod($relationMethod, $eloquentMethod, $relatedModelClass);
            }
        }

        return implode("\n\n", $methods);
    }

    /**
     * Build a single relationship method code block.
     *
     * @param string $methodName
     * @param string $relationMethod
     * @param string $relatedModelClass
     * @return string
     */
    protected function buildRelationshipMethod(string $methodName, string $relationMethod, string $relatedModelClass): string
    {
        return <<<PHP
    /**
     * {$relationMethod} relationship to {$relatedModelClass}.
     */
    public function {$methodName}()
    {
        return \$this->{$relationMethod}({$relatedModelClass}::class);
    }
PHP;
    }

    /**
     * Generate a method name for a relationship based on type and related model name.
     *
     * For "belongsTo" and "hasOne", we use the lower-case model name.
     * For "hasMany" and "belongsToMany", we append an "s" to pluralize.
     *
     * @param string $relationType
     * @param string $relatedModel
     * @return string
     */
    protected function generateRelationMethodName(string $relationType, string $relatedModel): string
    {
        $baseName = Str::camel($relatedModel);
        if (in_array($relationType, ['hasMany', 'belongsToMany'])) {
            // Simple pluralization: append 's'. For production code, consider using a proper pluralizer.
            return $baseName . 's';
        }
        return $baseName;
    }
}
