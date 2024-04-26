<?php

namespace DazzRick\HelloServer\Entity;

use Ramsey\Collection\Exception\InvalidPropertyOrMethod;

class GMessage extends Base
{
    protected ?string $_content = null;

    protected ?int $_sent = null;

    public function __get(string $name)
    {
        $parent = parent::__get($name);
        if($parent !== false) return $parent;
        return match($name)
        {
            'content' => $this->getContent(),
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
            'content' => $this->setContent($value),
            'sent' => $this->setSent($value),
            default => throw new InvalidPropertyOrMethod(sprintf("Unknown property: %s", $name))
        };
    }

    public function getData(bool $echo = false): array
    {
        $array = parent::getData($echo);
        if(!is_null($this->_content)) $array['content'] = $this->_content;
        if(!is_null($this->_sent)) $array['sent'] = $this->_sent;
        return $array;
    }

    public function isEmpty(): bool { return (parent::isEmpty() && is_null($this->_content) && is_null($this->_sent)); }

    public function getContent(): ?string { return $this->_content; }

    public function setContent(mixed $value): static { $this->_content = $value; return $this; }

    public function getSent(): ?int { return $this->_sent; }

    public function setSent(mixed $value): static { $this->_sent = $value; return $this; }

    public function new_instance(): static {return new static();}
}