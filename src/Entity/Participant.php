<?php

namespace DazzRick\HelloServer\Entity;

use Ramsey\Collection\Exception\InvalidPropertyOrMethod;

class Participant implements Entitable
{
    private ?int $_id = null;

    private ?string $_user = null;

    private ?string $_group = null;

    private ?bool $_is_active = null;

    private ?bool $_is_admin = null;

    private ?bool $_is_super = null;

    private ?string $_last_received = null;

    private ?string $_last_read = null;

    public function __get(string $name)
    {
        return match($name)
        {
            'id' => $this->_id,
            'user' => $this->getUser(),
            'group' => $this->getGroup(),
            'is_active' => $this->isActive(),
            'is_admin' => $this->isAdmin(),
            'is_super' => $this->isSuper(),
            'last_received' => $this->getLastReceived(),
            'last_read' => $this->getLastRead(),
            'data' => $this->getData(),
            default => throw new InvalidPropertyOrMethod(sprintf("Unknown property: %s", $name))
        };
    }

    public function __set(string $name, mixed $value)
    {
        return match($name)
        {
            'id' => $this->setId($value),
            'user' => $this->setUser($value),
            'group' => $this->setGroup($value),
            'is_active' => $this->isActive($value),
            'is_admin' => $this->isAdmin($value),
            'is_super' => $this->isSuper($value),
            'last_received' => $this->setLastReceived($value),
            'last_read' => $this->setLastRead($value),
            'data' => $this->setData($value),
            default => throw new InvalidPropertyOrMethod(sprintf("Unknown property: %s", $name))
        };
    }

    public function setId(int $id): static { $this->_id = $id; return $this; }

    public function setData(array $data): static { return setData($this, $data); }

    public function getData(): array
    {
        $array = [];

        if(!is_null($this->_user)) $array['user'] = $this->_user;
        if(!is_null($this->_group)) $array['group'] = $this->_group;
        if(!is_null($this->_is_active)) $array['is_active'] = $this->_is_active;
        if(!is_null($this->_is_admin)) $array['is_admin'] = $this->_is_admin;
        if(!is_null($this->_is_super)) $array['is_super'] = $this->_is_super;
        if(!is_null($this->_last_received)) $array['last_received'] = $this->_last_received;
        if(!is_null($this->_last_read)) $array['las_read'] = $this->_last_read;

        return $array;
    }

    public function isEmpty(): bool
        { return (is_null($this->_user) && is_null($this->_group) && is_null($this->_last_received) && is_null($this->_last_read)); }

    public function getUser(): ?string { return $this->_user; }

    public function setUser(?string $user): static { $this->_user = $user; return $this; }

    public function getGroup(): ?string { return $this->_group; }

    public function setGroup(?string $group): static { $this->_group = $group; return $this; }

    public function isActive(?bool $new_value = null, bool $set_null = false) : null|bool|static
    {
        if(is_null($new_value) && !$set_null) return $this->_is_active;
        else { $this->_is_active = $new_value; return $this; }
    }

    public function isAdmin(?bool $new_value = null, bool $set_null = false) : null|bool|static
    {
        if(is_null($new_value) && !$set_null) return $this->_is_admin;
        else { $this->_is_admin = $new_value; return $this; }
    }

    public function isSuper(?bool $new_value = null, bool $set_null = false) : null|bool|static
    {
        if(is_null($new_value) && !$set_null) return $this->_is_super;
        else { $this->_is_super = $new_value; return $this; }
    }

    public function getLastReceived(): ?int { return $this->_last_received; }

    public function setLastReceived(?int $last_received): static { $this->_last_received = $last_received; return $this; }

    public function getLastRead(): ?int { return $this->_last_read; }

    public function setLastRead(?int $last_read): static { $this->_last_read = $last_read; return $this; }
}