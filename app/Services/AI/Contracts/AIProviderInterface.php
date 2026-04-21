<?php

namespace App\Services\AI\Contracts;

interface AIProviderInterface
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function execute(string $module, string $taskType, array $payload): array;
}
