<?php

namespace DazzRick\HelloServer\Entity;

class File extends Message
{
    protected ?int $_opened = null;

    public function __get(string $name)
    {
        if($name === 'opened') return $this->getOpen();
        return parent::__get($name);
    }

    public function __set(string $name, mixed $value)
    {
        if($name === 'opened') return $this->setOpen($value);
        return parent::__set($name, $value);
    }

    public function getData(bool $echo = false): array
    {
        $data = parent::getData($echo);
        if(!is_null($this->_opened)) $data['opened'] = $this->getOpen();
        return $data;
    }

    public function isEmpty(): bool
    {
        return (!parent::isEmpty() && is_null($this->_opened));
    }

    public function getOpen(): ?int { return $this->_opened; }

    public function setOpen(mixed $value): static { $this->_opened = $value; return $this; }
}