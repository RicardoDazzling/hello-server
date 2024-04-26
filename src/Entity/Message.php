<?php

namespace DazzRick\HelloServer\Entity;

use Ramsey\Collection\Exception\InvalidPropertyOrMethod;

class Message extends GMessage
{

    protected ?int $_received = null;

    protected ?int $_read = null;

    public function __get(string $name)
    {
        $parent = parent::__get($name);
        if($parent !== false) return $parent;
        return match($name)
        {
            'received' => $this->getReceived(),
            'read' => $this->getRead(),
            default => throw new InvalidPropertyOrMethod(sprintf("Unknown property: %s", $name))
        };
    }

    public function __set(string $name, mixed $value)
    {
        $parent = parent::__set($name, $value);
        if($parent !== false) return $parent;
        return match($name)
        {
            'received' => $this->setReceived($value),
            'read' => $this->setRead($value),
            default => throw new InvalidPropertyOrMethod(sprintf("Unknown property: %s", $name))
        };
    }

    public function getData(bool $echo = false): array
    {
        $array = parent::getData($echo);
        if(!is_null($this->_received)) $array['received'] = $this->_received;
        if(!is_null($this->_read)) $array['read'] = $this->_read;
        return $array;
    }

    public function getReceived(): ?int { return $this->_received; }

    public function setReceived(mixed $value): static { $this->_received = $value; return $this; }

    public function getRead(): ?int { return $this->_read; }

    public function setRead(mixed $value): static { $this->_read = $value; return $this; }
}