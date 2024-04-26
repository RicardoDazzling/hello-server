<?php

namespace DazzRick\HelloServer\Entity;

use Ramsey\Collection\Exception\InvalidPropertyOrMethod;

class Group implements Entitable
{
    protected ?int $_id = null;

    protected ?string $_uuid = null;

    protected ?string $_photo = null;

    protected ?string $_name = null;

    protected ?string $_description = null;

    protected ?int $_creation = null;

    public function __get(string $name)
    {
        return match($name)
        {
            'id' => $this->_id,
            'uuid' => $this->getUuid(),
            'photo' => $this->getPhoto(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'creation' => $this->getCreation(),
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
            'photo' => $this->setPhoto($value),
            'name' => $this->setName($value),
            'description' => $this->setDescription($value),
            'creation' => $this->setCreation($value),
            'data' => $this->setData($value),
            default => throw new InvalidPropertyOrMethod(sprintf("Unknown property: %s", $name))
        };
    }

    public function setId(int $id): static { $this->_id = $id; return $this; }

    public function setUuid(?string $uuid): static
    {
        if(is_null($this->_uuid) || is_null($uuid)) { $this->_uuid = $uuid; return $this; }
        else throw new InvalidPropertyOrMethod('UUID property already defined.');
    }

    public function getUuid(): ?string { return $this->_uuid; }

    public function setPhoto(?string $photo): static { $this->_photo = $photo; return $this; }

    public function getPhoto(): ?string { return $this->_photo; }

    public function setName(?string $name): static { $this->_name = $name; return $this; }

    public function getName(): ?string { return $this->_name; }

    public function setDescription(?string $description): static { $this->_description = $description; return $this; }

    public function getDescription(): ?string { return $this->_description; }

    public function setCreation(?string $value): static { $this->_creation = $value; return $this; }

    public function getCreation(): ?int { return $this->_creation; }

    public function setData(array $data): static { return setData($this, $data); }

    public function getData(): array
    {
        $array = [];
        if(!is_null($this->_uuid)) $array['uuid'] = $this->_uuid;
        if(!is_null($this->_photo)) $array['photo'] = $this->_photo;
        if(!is_null($this->_name)) $array['name'] = $this->_name;
        if(!is_null($this->_description)) $array['description'] = $this->_description;
        if(!is_null($this->_creation)) $array['creation'] = $this->_creation;
        return $array;
    }

    public function isEmpty(): bool
    {
        return (is_null($this->_uuid) && is_null($this->_photo) && is_null($this->_name) && is_null($this->_description)
            && is_null($this->_creation));
    }
}