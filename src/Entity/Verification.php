<?php

namespace DazzRick\HelloServer\Entity;

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

    public function setId(int $id): static { $this->_id = $id; return $this; }

    public function setUuid(string $uuid): static
    {
        if(is_null($this->_uuid)) { $this->_uuid = $uuid; return $this; }
        else throw new InvalidPropertyOrMethod('UUID property already defined.');
    }

    public function getUuid(): string { return $this->_uuid; }

    public function setData(array $data): static { return setData($this, $data); }

    public function getData(): array
    {
        $array = [];
        if(!is_null($this->_uuid)) $array['uuid'] = $this->_uuid;
        if(!is_null($this->_code)) $array['code'] = $this->_code;
        if(!is_null($this->_last_try)) $array['last_try'] = $this->_last_try;
        if(!is_null($this->_try_number)) $array['email'] = $this->_try_number;
        return $array;
    }

    public function isEmpty(): bool
    {
        return (is_null($this->_uuid) && is_null($this->_code) && is_null($this->_last_try) && is_null($this->_try_number));
    }

    public function getCode(): ?string { return $this->_code; }

    public function setCode(mixed $code): static { $this->_code = $code; return $this; }

    public function getLastTry(): ?int { return $this->_last_try; }

    public function setLastTry(mixed $last_try): static { $this->_last_try = $last_try; return $this; }

    public function getTryNumber(): ?int { return $this->_try_number; }

    public function setTryNumber(mixed $value): static { $this->_try_number = $value; return $this; }
}