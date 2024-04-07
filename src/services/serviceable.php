<?php

namespace DazzRick\HelloServer\Services;

use DazzRick\HelloServer\Entity\Entitable;

interface Serviceable
{
    public const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public function create(array $data): Entitable;

    public function retrieve_all(): array;

    public function retrieve(string $uuid): Entitable;

    public function update(array $postBody, string $uuid): Entitable;

    public function remove(?string $uuid): Entitable;
}