<?php

namespace App\Services\ModelGeneration\Contracts;

interface ModelBuilderInterface
{
    /**
     * Set the model name
     *
     * @param string $name
     * @return self
     */
    public function setName(string $name): self;

    /**
     * Set the model namespace
     *
     * @param string $namespace
     * @return self
     */
    public function setNamespace(string $namespace): self;

    /**
     * Add a trait to the model
     *
     * @param string $trait
     * @return self
     */
    public function addTrait(string $trait): self;

    /**
     * Add a use statement
     *
     * @param string $class
     * @return self
     */
    public function addUse(string $class): self;

    /**
     * Add a property to the model
     *
     * @param string $name
     * @param mixed $value
     * @param string $visibility
     * @return self
     */
    public function addProperty(string $name, $value, string $visibility = 'protected'): self;

    /**
     * Add a method to the model
     *
     * @param string $name
     * @param array $parameters
     * @param string $body
     * @param string $visibility
     * @param string $returnType
     * @return self
     */
    public function addMethod(
        string $name,
        array $parameters,
        string $body,
        string $visibility = 'public',
        string $returnType = 'void'
    ): self;

    /**
     * Add a relationship method
     *
     * @param string $name
     * @param string $type
     * @param string $relatedModel
     * @param string|null $foreignKey
     * @param string|null $localKey
     * @return self
     */
    public function addRelationship(
        string $name,
        string $type,
        string $relatedModel,
        ?string $foreignKey = null,
        ?string $localKey = null
    ): self;

    /**
     * Add validation rules
     *
     * @param array $rules
     * @return self
     */
    public function addValidationRules(array $rules): self;

    /**
     * Add attribute casts
     *
     * @param array $casts
     * @return self
     */
    public function addCasts(array $casts): self;

    /**
     * Add fillable attributes
     *
     * @param array $attributes
     * @return self
     */
    public function addFillable(array $attributes): self;

    /**
     * Add guarded attributes
     *
     * @param array $attributes
     * @return self
     */
    public function addGuarded(array $attributes): self;

    /**
     * Add hidden attributes
     *
     * @param array $attributes
     * @return self
     */
    public function addHidden(array $attributes): self;

    /**
     * Add appends attributes
     *
     * @param array $attributes
     * @return self
     */
    public function addAppends(array $attributes): self;

    /**
     * Build the model class
     *
     * @return string
     */
    public function build(): string;
} 