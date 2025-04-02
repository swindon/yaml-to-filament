<?php

namespace Swindon\YamlToFilament\Support;

class Schema
{
    /**
     * Validate the blueprint data structure.
     *
     * This method ensures the YAML blueprint contains valid definitions for:
     * - models (each with optional 'fillable', 'columns', and 'relationships')
     * - resources (each with optional 'form' and 'table' definitions)
     * - widgets (each with optional 'title', 'view', and 'properties')
     *
     * @param array $data
     * @return bool
     * @throws \InvalidArgumentException If any validation rule fails.
     */
    public static function validate(array $data): bool
    {
        if (!is_array($data)) {
            throw new \InvalidArgumentException("Blueprint data must be an array.");
        }

        // Validate "models" section.
        if (isset($data['models'])) {
            if (!is_array($data['models'])) {
                throw new \InvalidArgumentException("'models' must be an array.");
            }
            foreach ($data['models'] as $modelName => $modelDefinition) {
                if (!is_array($modelDefinition)) {
                    throw new \InvalidArgumentException("Definition for model '{$modelName}' must be an array.");
                }
                // "fillable" should be an array if provided.
                if (isset($modelDefinition['fillable']) && !is_array($modelDefinition['fillable'])) {
                    throw new \InvalidArgumentException("The 'fillable' key for model '{$modelName}' must be an array.");
                }
                // "columns" should be an array if provided.
                if (isset($modelDefinition['columns']) && !is_array($modelDefinition['columns'])) {
                    throw new \InvalidArgumentException("The 'columns' key for model '{$modelName}' must be an array.");
                }
                // "relationships" should be an array if provided.
                if (isset($modelDefinition['relationships'])) {
                    if (!is_array($modelDefinition['relationships'])) {
                        throw new \InvalidArgumentException("The 'relationships' key for model '{$modelName}' must be an array.");
                    }
                    $allowedRelations = ['belongsTo', 'hasMany', 'hasOne', 'belongsToMany'];
                    foreach ($modelDefinition['relationships'] as $relationType => $relatedModels) {
                        if (!in_array($relationType, $allowedRelations, true)) {
                            throw new \InvalidArgumentException("Invalid relationship type '{$relationType}' for model '{$modelName}'. Allowed types: " . implode(', ', $allowedRelations) . ".");
                        }
                        // Allow a single string or an array of strings.
                        if (!is_array($relatedModels) && !is_string($relatedModels)) {
                            throw new \InvalidArgumentException("Relationship '{$relationType}' for model '{$modelName}' must be an array or a string.");
                        }
                        if (is_array($relatedModels)) {
                            foreach ($relatedModels as $relatedModel) {
                                if (!is_string($relatedModel)) {
                                    throw new \InvalidArgumentException("Each related model in '{$relationType}' for model '{$modelName}' must be a string.");
                                }
                            }
                        }
                    }
                }
            }
        }

        // Validate "resources" section.
        if (isset($data['resources'])) {
            if (!is_array($data['resources'])) {
                throw new \InvalidArgumentException("'resources' must be an array.");
            }
            foreach ($data['resources'] as $resourceName => $resourceDefinition) {
                if (!is_array($resourceDefinition)) {
                    throw new \InvalidArgumentException("Definition for resource '{$resourceName}' must be an array.");
                }
                // Validate form fields if provided.
                if (isset($resourceDefinition['form'])) {
                    if (!is_array($resourceDefinition['form'])) {
                        throw new \InvalidArgumentException("The 'form' key for resource '{$resourceName}' must be an array.");
                    }
                    foreach ($resourceDefinition['form'] as $index => $field) {
                        if (!is_array($field)) {
                            throw new \InvalidArgumentException("Each form field in resource '{$resourceName}' must be an array. Error at index {$index}.");
                        }
                        if (!isset($field['field']) || !is_string($field['field'])) {
                            throw new \InvalidArgumentException("Each form field in resource '{$resourceName}' must have a 'field' key of type string. Error at index {$index}.");
                        }
                        if (!isset($field['type']) || !is_string($field['type'])) {
                            throw new \InvalidArgumentException("Each form field in resource '{$resourceName}' must have a 'type' key of type string. Error at field '{$field['field']}'.");
                        }
                    }
                }
                // Validate table columns if provided.
                if (isset($resourceDefinition['table'])) {
                    if (!is_array($resourceDefinition['table'])) {
                        throw new \InvalidArgumentException("The 'table' key for resource '{$resourceName}' must be an array.");
                    }
                    foreach ($resourceDefinition['table'] as $index => $column) {
                        if (!is_array($column)) {
                            throw new \InvalidArgumentException("Each table column in resource '{$resourceName}' must be an array. Error at index {$index}.");
                        }
                        if (!isset($column['column']) || !is_string($column['column'])) {
                            throw new \InvalidArgumentException("Each table column in resource '{$resourceName}' must have a 'column' key of type string. Error at index {$index}.");
                        }
                    }
                }
            }
        }

        // Validate "widgets" section.
        if (isset($data['widgets'])) {
            if (!is_array($data['widgets'])) {
                throw new \InvalidArgumentException("'widgets' must be an array.");
            }
            foreach ($data['widgets'] as $widgetName => $widgetDefinition) {
                if (!is_array($widgetDefinition)) {
                    throw new \InvalidArgumentException("Definition for widget '{$widgetName}' must be an array.");
                }
                if (isset($widgetDefinition['title']) && !is_string($widgetDefinition['title'])) {
                    throw new \InvalidArgumentException("The 'title' for widget '{$widgetName}' must be a string.");
                }
                if (isset($widgetDefinition['view']) && !is_string($widgetDefinition['view'])) {
                    throw new \InvalidArgumentException("The 'view' for widget '{$widgetName}' must be a string.");
                }
                if (isset($widgetDefinition['properties']) && !is_array($widgetDefinition['properties'])) {
                    throw new \InvalidArgumentException("The 'properties' for widget '{$widgetName}' must be an array.");
                }
            }
        }

        return true;
    }
}
