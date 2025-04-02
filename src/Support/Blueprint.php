<?php

namespace Swindon\YamlToFilament\Support;

class Blueprint
{
    /**
     * The blueprint data.
     *
     * @var array
     */
    protected $data;

    /**
     * Blueprint constructor.
     *
     * Validates and stores the blueprint data.
     *
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public function __construct(array $data)
    {
        // Validate the blueprint structure.
        Schema::validate($data);
        $this->data = $data;
    }

    /**
     * Factory method to create a Blueprint instance.
     *
     * @param array $data
     * @return self
     */
    public static function make(array $data): self
    {
        return new self($data);
    }

    /**
     * Retrieve the entire blueprint data.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get all model definitions.
     *
     * @return array
     */
    public function getModels(): array
    {
        return $this->data['models'] ?? [];
    }

    /**
     * Get a specific model definition by name.
     *
     * @param string $modelName
     * @return array|null
     */
    public function getModel(string $modelName): ?array
    {
        $models = $this->getModels();
        return $models[$modelName] ?? null;
    }

    /**
     * Get all resource definitions.
     *
     * @return array
     */
    public function getResources(): array
    {
        return $this->data['resources'] ?? [];
    }

    /**
     * Get a specific resource definition by name.
     *
     * @param string $resourceName
     * @return array|null
     */
    public function getResource(string $resourceName): ?array
    {
        $resources = $this->getResources();
        return $resources[$resourceName] ?? null;
    }

    /**
     * Get all widget definitions.
     *
     * @return array
     */
    public function getWidgets(): array
    {
        return $this->data['widgets'] ?? [];
    }

    /**
     * Get the relationships defined for a specific model.
     *
     * @param string $modelName
     * @return array
     */
    public function getRelationships(string $modelName): array
    {
        $model = $this->getModel($modelName);
        return $model['relationships'] ?? [];
    }
    
    // Additional helper methods can be added here as needed.
}
