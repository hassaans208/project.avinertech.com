<?php

namespace App\Services\Contracts;

interface ServiceInterface
{
    /**
     * Execute the service operation
     *
     * @param array $data
     * @return mixed
     */
    public function execute(array $data = []);
} 