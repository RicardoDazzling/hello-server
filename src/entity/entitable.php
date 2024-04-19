<?php

namespace DazzRick\HelloServer\Entity;

interface Entitable
{
    public function __get(string $name);

    public function __set(string $name, mixed $value);

    public function setId(int $id): static;

    public function setData(array $data): static;

    public function getData(): array;

    public function isEmpty(): bool;
}

function setData(mixed $self, array $data){
    if(count($data) > 0)
    {
        foreach ($data as $data_name => $data_value)
        {
            $self->__set($data_name, $data_value);
        }
    }
    return $self;
}

function setEmpty(mixed $self, array $data){
    if(count($data) > 0)
    {
        foreach ($data as $data_name => $data_value)
        {
            if (is_null($self->__get($data_name)))
                $self->__set($data_name, $data_value);
        }
    }
    return $self;
}