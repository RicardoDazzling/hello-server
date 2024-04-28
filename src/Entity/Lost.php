<?php

namespace DazzRick\HelloServer\Entity;

use Ramsey\Collection\Exception\InvalidPropertyOrMethod;

class Lost extends Base
{
    protected ?string $_type = null;

    protected ?int $_sent = null;

    public function __get(string $name)
    {
        $parent = parent::__get($name);
        if($parent !== false) return $parent;
        return match($name)
        {
            'type' => $this->getType(),
            'sent' => $this->getSent(),
            default => throw new InvalidPropertyOrMethod(sprintf("Unknown property: %s", $name))
        };
    }

    public function __set(string $name, mixed $value)
    {
        $parent = parent::__set($name, $value);
        if($parent !== false) return $parent;
        return match($name)
        {
            'type' => $this->setType($value),
            'sent' => $this->setSent($value),
            default => throw new InvalidPropertyOrMethod(sprintf("Unknown property: %s", $name))
        };
    }

    public function getData(bool $echo = false): array
    {
        $array = parent::getData($echo);
        if(!is_null($this->_type)) $array['type'] = $this->_type;
        if(!is_null($this->_sent)) $array['sent'] = $this->_sent;
        return $array;
    }

    public function isEmpty(): bool
    {
        return (parent::isEmpty() && is_null($this->_type) && is_null($this->_sent));
    }

    public function getType(): ?string { return $this->_type; }

    public function setType(mixed $type): static { $this->_type = $type; return $this; }

    public function getSent(): ?string { return $this->_sent; }

    public function setSent(mixed $sent): static { $this->_sent = $sent; return $this; }
}