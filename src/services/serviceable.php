<?php

namespace DazzRick\HelloServer\Services;

use DazzRick\HelloServer\Entity\Entitable;

interface Serviceable
{
    public const string DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public function create(mixed $data): Entitable;

    public function retrieve_all(): array;

    public function retrieve(string $uuid): Entitable;

    public function update(mixed $postBody, string $uuid): Entitable;

    public function remove(?string $uuid): Entitable|true;
}