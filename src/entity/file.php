<?php

namespace DazzRick\HelloServer\Entity;

use Override;

class File extends Message
{
    protected ?int $_open = null;

    #[Override] public function __get(string $name)
    {
        if($name === 'open') return $this->getOpen();
        return parent::__get($name);
    }

    #[Override] public function __set(string $name, mixed $value)
    {
        if($name === 'open') return $this->setOpen($value);
        return parent::__set($name, $value);
    }

    #[Override] public function getData(): array
    {
        $data = parent::getData();
        if(!empty($this->_open)) $data['open'] = $this->getOpen();
        return $data;
    }

    #[Override] public function isEmpty(): bool
    {
        return (!parent::isEmpty() && empty($this->_open));
    }

    public function getOpen(): ?int { return $this->_open; }

    public function setOpen(mixed $value): static { $this->_open = $value; return $this; }
}