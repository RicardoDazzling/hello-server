<?php

namespace DazzRick\HelloServer\Entity;

use Ramsey\Collection\Exception\InvalidPropertyOrMethod;

class Message extends Base
{
    protected ?string $_uuid = null;

    protected ?string $_content = null;

    protected ?int $_send = null;

    protected ?int $_received = null;

    protected ?int $_read = null;

    public function __get(string $name)
    {
        $parent = parent::__get($name);
        if($parent !== false) return $parent;
        return match($name)
        {
            'uuid' => $this->getUuid(),
            'content' => $this->getContent(),
            'send' => $this->getSend(),
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
            'uuid' => $this->setUuid($value),
            'content' => $this->setContent($value),
            'send' => $this->setSend($value),
            'received' => $this->setReceived($value),
            'read' => $this->setRead($value),
            default => throw new InvalidPropertyOrMethod(sprintf("Unknown property: %s", $name))
        };
    }

    public function setUuid(string $uuid): static
    {
        if(empty($this->_uuid)) { $this->_uuid = $uuid; return $this; }
        throw new InvalidPropertyOrMethod('UUID property already defined.');
    }

    public function getUuid(): ?string { return $this->_uuid; }

    public function getData(): array
    {
        $array = parent::getData();
        if(!empty($this->_uuid)) $array['uuid'] = $this->_uuid;
        if(!empty($this->_content)) $array['content'] = $this->_content;
        if(!empty($this->_send)) $array['send'] = $this->_send;
        if(!empty($this->_received)) $array['received'] = $this->_received;
        if(!empty($this->_read)) $array['read'] = $this->_read;
        return $array;
    }

    public function isEmpty(): bool
    {
        return (empty($this->_uuid) && !(parent::isEmpty()) && empty($this->_content) && empty($this->_send));
    }

    public function getContent(): ?string { return $this->_content; }

    public function setContent(mixed $value): static { $this->_content = $value; return $this; }

    public function getSend(): ?int { return $this->_send; }

    public function setSend(mixed $value): static { $this->_send = $value; return $this; }

    public function getReceived(): ?int { return $this->_received; }

    public function setReceived(mixed $value): static { $this->_received = $value; return $this; }

    public function getRead(): ?int { return $this->_read; }

    public function setRead(mixed $value): static { $this->_read = $value; return $this; }
}