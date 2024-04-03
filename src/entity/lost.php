<?php

namespace DazzRick\HelloServer\Entity;

use Override;
use Ramsey\Collection\Exception\InvalidPropertyOrMethod;

class Lost extends Base
{
    protected ?string $_type = null;

    protected ?int $_send = null;

    public function __get(string $name)
    {
        $parent = parent::__get($name);
        if($parent !== false) return $parent;
        return match($name)
        {
            'type' => $this->getType(),
            'send' => $this->getSend(),
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
            'send' => $this->setSend($value),
            default => throw new InvalidPropertyOrMethod(sprintf("Unknown property: %s", $name))
        };
    }

    #[Override] public function getData(): array
    {
        $array = parent::getData();
        if(!empty($this->_type)) $array['type'] = $this->_type;
        if(!empty($this->_send)) $array['send'] = $this->_send;
        return $array;
    }

    #[Override] public function isEmpty(): bool
    {
        return (!(parent::isEmpty()) && empty($this->_type) && empty($this->_send));
    }

    public function getType(): ?string { return $this->_type; }

    public function setType(mixed $type): static { $this->_type = $type; return $this; }

    public function getSend(): ?string { return $this->_send; }

    public function setSend(mixed $send): static { $this->_send = $send; return $this; }
}