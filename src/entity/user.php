<?php

namespace DazzRick\HelloServer\Entity;

use Ramsey\Collection\Exception\InvalidPropertyOrMethod;

class User implements Entitable
{
    protected ?int $_id = null;

    protected ?string $_uuid = null;

    protected ?bool $_online = null;

    protected ?string $_name = null;

    protected ?string $_email = null;

    protected ?string $_default = null;

    protected ?int $_creation_date = null;

    public function __get(string $name)
    {
        return match($name)
        {
            'id' => $this->_id,
            'uuid' => $this->getUuid(),
            'online' => $this->getOnline(),
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'default' => $this->getDefault(),
            'creation_date' => $this->getCreationDate(),
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
            'online' => $this->setOnline($value),
            'name' => $this->setName($value),
            'email' => $this->setEmail($value),
            'default' => $this->setDefault($value),
            'creation_date' => $this->setCreationDate($value),
            'data' => $this->setData($value),
            default => throw new InvalidPropertyOrMethod(sprintf("Unknown property: %s", $name))
        };
    }

    public function setId(int $id): static { $this->_id = $id; return $this; }

    public function setUuid(string $uuid): static
    {
        if(empty($this->_uuid)) { $this->_uuid = $uuid; return $this; }
        else throw new InvalidPropertyOrMethod('UUID property already defined.');
    }

    public function getUuid(): ?string { return $this->_uuid; }

    public function setOnline(bool $online): static { $this->_online = $online; return $this; }

    public function getOnline(): ?string { return $this->_online; }

    public function setName(string $name): static { $this->_name = $name; return $this; }

    public function getName(): ?string { return $this->_name; }

    public function setEmail(string $email): static { $this->_email = $email; return $this; }

    public function getEmail(): ?string { return $this->_email; }

    public function setDefault(string $default): static { $this->_default = $default; return $this; }

    public function getDefault(): ?string { return $this->_default; }

    public function setCreationDate(string $value): static { $this->_creation_date = $value; return $this; }

    public function getCreationDate(): ?int { return $this->_creation_date; }

    public function setData(array $data): static { return setData($this, $data); }

    public function getData(): array
    {
        $array = [];
        if(!empty($this->_uuid)) $array['uuid'] = $this->_uuid;
        if(!empty($this->_online)) $array['online'] = $this->_online;
        if(!empty($this->_name)) $array['name'] = $this->_name;
        if(!empty($this->_email)) $array['email'] = $this->_email;
        if(!empty($this->_default)) $array['default'] = $this->_default;
        if(!empty($this->_creation_date)) $array['creation_date'] = $this->_creation_date;
        return $array;
    }

    public function isEmpty(): bool
    {
        return (empty($this->_uuid) && empty($this->_online) && empty($this->_name) && empty($this->_email)
            && empty($this->_default) && empty($this->_creation_date));
    }
}