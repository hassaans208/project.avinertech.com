<?php

namespace App\Services\ModelGeneration\Contracts;

interface ModelGeneratorInterface
{
    /**
     * Generate a model from the given definition
     *
     * @param array $modelDefinition
     * @return array{
     *     success: bool,
     *     model_content: string,
     *     file_name: string,
     *     namespace: string,
     *     class_name: string,
     *     relationships: array,
     *     validation_rules: array
     * }
     */
    public function generate(array $modelDefinition): array;

    /**
     * Generate model relationships
     *
     * @param array $modelDefinition
     * @param array $relatedModels
     * @return array
     */
    public function generateRelationships(array $modelDefinition, array $relatedModels): array;

    /**
     * Generate validation rules for the model
     *
     * @param array $modelDefinition
     * @return array
     */
    public function generateValidationRules(array $modelDefinition): array;

    /**
     * Generate model attributes and casts
     *
     * @param array $modelDefinition
     * @return array
     */
    public function generateAttributes(array $modelDefinition): array;
} 