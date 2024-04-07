<?php

namespace DazzRick\HelloServer\Entity;

use Ramsey\Collection\Exception\InvalidPropertyOrMethod;

class Call extends Base
{
    protected ?string $_image = null;

    protected ?string $_audio = null;

    public function __get(string $name)
    {
        $parent = parent::__get($name);
        if($parent !== false) return $parent;
        return match($name)
        {
            'image' => $this->getImage(),
            'audio' => $this->getAudio(),
            default => throw new InvalidPropertyOrMethod(sprintf("Unknown property: %s", $name))
        };
    }

    public function __set(string $name, mixed $value)
    {
        $parent = parent::__set($name, $value);
        if($parent !== false) return $parent;
        return match($name)
        {
            'uuid' => $this->setImage($value),
            'content' => $this->setAudio($value),
            default => throw new InvalidPropertyOrMethod(sprintf("Unknown property: %s", $name))
        };
    }

    public function getData(): array
    {
        $array = parent::getData();
        if(!empty($this->_image)) $array['image'] = $this->_image;
        if(!empty($this->_audio)) $array['audio'] = $this->_audio;
        return $array;
    }

    public function isEmpty(): bool
    {
        return (!(parent::isEmpty()) && empty($this->_image) && empty($this->_audio));
    }

    public function getImage(): ?string { return $this->_image; }

    public function setImage(mixed $image): static { $this->_image = $image; return $this; }

    public function getAudio(): ?string { return $this->_audio; }

    public function setAudio(mixed $audio): static { $this->_audio = $audio; return $this; }
}