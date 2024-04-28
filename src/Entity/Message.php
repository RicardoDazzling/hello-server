<?php

namespace DazzRick\HelloServer\Entity;

use Ramsey\Collection\Exception\InvalidPropertyOrMethod;

class Message extends GMessage
{

    protected ?int $_received = null;

    protected ?int $_read = null;

    public function __get(string $name)
    {
        return match($name)
        {
            'received' => $this->getReceived(),
            'read' => $this->getRead(),
            default => parent::__get($name)
        };
    }

    public function __set(string $name, mixed $value)
    {
        return match($name)
        {
            'received' => $this->setReceived($value),
            'read' => $this->setRead($value),
            default => parent::__set($name, $value)
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