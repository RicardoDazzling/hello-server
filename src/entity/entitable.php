<?php

namespace DazzRick\HelloServer\Entity;

interface Entitable
{
    public function __get(string $name);

    public function __set(string $name, mixed $value);

    public function setId(int $id): self;

    public function setUuid(string $uuid): self;

    public function getUuid(): string;

    public function setData(array $data): self;

    public function getData(): array;

    public function isEmpty(): bool;
}