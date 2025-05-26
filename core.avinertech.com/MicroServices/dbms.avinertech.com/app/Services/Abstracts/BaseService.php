<?php

namespace App\Services\Abstracts;

use App\Services\Contracts\ServiceInterface;
use Illuminate\Support\Facades\Log;

abstract class BaseService implements ServiceInterface
{
    /**
     * @var array
     */
    protected array $data = [];

    /**
     * @var mixed
     */
    protected $result;

    /**
     * Set the data for the service
     *
     * @param array $data
     * @return self
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get the service result
     *
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Handle any errors that occur during service execution
     *
     * @param \Throwable $e
     * @return void
     */
    protected function handleError(\Throwable $e): void
    {
        Log::error('Service Error: ' . get_class($this), [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        throw $e;
    }

    /**
     * Execute the service operation
     *
     * @param array $data
     * @return mixed
     */
    public function execute(array $data = [])
    {
        try {
            $this->setData($data);
            $this->validate();
            $this->process();
            return $this->getResult();
        } catch (\Throwable $e) {
            $this->handleError($e);
        }
    }

    /**
     * Validate the service data
     *
     * @return void
     */
    abstract protected function validate(): void;

    /**
     * Process the service operation
     *
     * @return void
     */
    abstract protected function process(): void;
} 