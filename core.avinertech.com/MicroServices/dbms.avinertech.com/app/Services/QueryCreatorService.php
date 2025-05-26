<?php

namespace App\Services;

use App\Services\Abstracts\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class QueryCreatorService extends BaseService
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * @var array
     */
    protected array $filters = [];

    /**
     * @var array
     */
    protected array $relations = [];

    /**
     * @var array
     */
    protected array $sorts = [];

    /**
     * Set the model for the query
     *
     * @param Model $model
     * @return self
     */
    public function setModel(Model $model): self
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Set the filters for the query
     *
     * @param array $filters
     * @return self
     */
    public function setFilters(array $filters): self
    {
        $this->filters = $filters;
        return $this;
    }

    /**
     * Set the relations to be loaded
     *
     * @param array $relations
     * @return self
     */
    public function setRelations(array $relations): self
    {
        $this->relations = $relations;
        return $this;
    }

    /**
     * Set the sorting parameters
     *
     * @param array $sorts
     * @return self
     */
    public function setSorts(array $sorts): self
    {
        $this->sorts = $sorts;
        return $this;
    }

    /**
     * Validate the service data
     *
     * @return void
     */
    protected function validate(): void
    {
        if (!isset($this->model)) {
            throw new \InvalidArgumentException('Model must be set before creating query');
        }
    }

    /**
     * Process the query creation
     *
     * @return void
     */
    protected function process(): void
    {
        $query = $this->model->newQuery();

        $this->applyFilters($query);
        $this->applyRelations($query);
        $this->applySorting($query);

        $this->result = $query;
    }

    /**
     * Apply filters to the query
     *
     * @param Builder $query
     * @return void
     */
    protected function applyFilters(Builder $query): void
    {
        foreach ($this->filters as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }
    }

    /**
     * Apply relations to the query
     *
     * @param Builder $query
     * @return void
     */
    protected function applyRelations(Builder $query): void
    {
        if (!empty($this->relations)) {
            $query->with($this->relations);
        }
    }

    /**
     * Apply sorting to the query
     *
     * @param Builder $query
     * @return void
     */
    protected function applySorting(Builder $query): void
    {
        foreach ($this->sorts as $field => $direction) {
            $query->orderBy($field, $direction);
        }
    }
} 