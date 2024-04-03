<?php

namespace DazzRick\HelloServer\Entity;

use Override;
use Ramsey\Collection\Exception\InvalidPropertyOrMethod;

class Base implements Entitable
{
    protected ?int $_id = null;

    protected ?string $_from = null;

    protected ?string $_to = null;

    #[Override] public function __get(string $name)
    {
        return match($name)
        {
            'id' => $this->_id,
            'from' => $this->getFrom(),
            'to' => $this->getTo(),
            'data' => $this->getData(),
            default => false
        };
    }

    #[Override] public function __set(string $name, mixed $value)
    {
        return match($name)
        {
            'id' => $this->setId($value),
            'from' => $this->setFrom($value),
            'to' => $this->setTo($value),
            'data' => $this->internalSetData($value),
            default => false
        };
    }

    /**
     * @param array $data
     * @return static
     */
    #[Override] public function setData(array $data): static { return setData($this, $data); }

    #[Override] public function getData(): array
    {
        $array = [];
        if(!empty($this->_from)) $array['from'] = $this->_from;
        if(!empty($this->_to)) $array['to'] = $this->_to;
        return $array;
    }

    /**
     * @param int $id
     * @return static
     */
    #[Override] public function setId(int $id): static { $this->_id = $id; return new static(); }

    #[Override] public function isEmpty(): bool { return (empty($this->_from) && empty($this->_to)); }

    protected function internalSetData(mixed $value): static
    {
        if(!is_array($value)) throw new InvalidPropertyOrMethod(sprintf(
                'Data value is from "%s" type and array type is required.', gettype($value)));
        return $this->setData($value);
    }

    public function getFrom(): ?string { return $this->_from; }

    public function setFrom(mixed $value): static { $this->_from = $value; return new static(); }

    public function getTo(): ?string { return $this->_to; }

    public function setTo(mixed $value): static { $this->_to = $value; return new static(); }

}