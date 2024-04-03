<?php

namespace DazzRick\HelloServer\Entity;

use Override;
use Ramsey\Collection\Exception\InvalidPropertyOrMethod;

class Verification implements Entitable
{
    protected ?int $_id = null;

    protected ?string $_uuid = null;

    protected ?string $_code = null;

    protected ?int $_last_try = null;

    protected ?int $_try_number = null;

    public function __get(string $name)
    {
        return match($name)
        {
            'id' => $this->_id,
            'uuid' => $this->getUuid(),
            'code' => $this->getCode(),
            'last_try' => $this->getLastTry(),
            'try_number' => $this->getTryNumber(),
            'data' => $this->getData(),
            default => throw new InvalidPropertyOrMethod(sprintf("Unknown property: %s", $name))
        };
    }

    public function __set(string $name, mixed $value)
    {
        return match($name)
        {
            'id' => $this->setId($value),
            'uuid' => $this->setUuid($value),
            'code' => $this->setCode($value),
            'last_try' => $this->setLastTry($value),
            'try_number' => $this->setTryNumber($value),
            'data' => $this->setData($value),
            default => throw new InvalidPropertyOrMethod(sprintf("Unknown property: %s", $name))
        };
    }

    #[Override] public function setId(int $id): static { $this->_id = $id; return $this; }

    public function setUuid(string $uuid): static
    {
        if(empty($this->_uuid)) { $this->_uuid = $uuid; return $this; }
        else throw new InvalidPropertyOrMethod('UUID property already defined.');
    }

    public function getUuid(): string { return $this->_uuid; }

    #[Override] public function setData(array $data): static { return setData($this, $data); }

    #[Override] public function getData(): array
    {
        $array = [];
        if(!empty($this->_uuid)) $array['uuid'] = $this->_uuid;
        if(!empty($this->_code)) $array['code'] = $this->_code;
        if(!empty($this->_last_try)) $array['last_try'] = $this->_last_try;
        if(!empty($this->_try_number)) $array['email'] = $this->_try_number;
        return $array;
    }

    #[Override] public function isEmpty(): bool
    {
        return (empty($this->_uuid) && empty($this->_code) && empty($this->_last_try) && empty($this->_try_number));
    }

    public function getCode(): ?string { return $this->_code; }

    public function setCode(mixed $code): static { $this->_code = $code; return $this; }

    public function getLastTry(): ?int { return $this->_last_try; }

    public function setLastTry(mixed $last_try): static { $this->_last_try = $last_try; return $this; }

    public function getTryNumber(): ?int { return $this->_try_number; }

    public function setTryNumber(mixed $value): static { $this->_try_number = $value; return $this; }
}