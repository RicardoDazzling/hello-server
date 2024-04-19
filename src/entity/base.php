<?php

namespace DazzRick\HelloServer\Entity;

use Ramsey\Collection\Exception\InvalidPropertyOrMethod;

class Base implements Entitable
{
    protected ?int $_id = null;

    protected ?string $_uuid = null;

    protected ?string $_from_uuid = null;

    protected ?string $_to_uuid = null;

    protected ?string $_from_email = null;

    protected ?string $_to_email = null;

    public function __get(string $name)
    {
        return match($name)
        {
            'id' => $this->_id,
            'uuid' => $this->getUuid(),
            'from_uuid' => $this->getFromUuid(),
            'to_uuid' => $this->getToUuid(),
            'from_email' => $this->getFromEmail(),
            'to_email' => $this->getToEmail(),
            'data' => $this->getData(),
            default => false
        };
    }

    public function __set(string $name, mixed $value)
    {
        return match($name)
        {
            'id' => $this->setId($value),
            'uuid' => $this->setUuid($value),
            'from_uuid' => $this->setFromUuid($value),
            'to_uuid' => $this->setToUuid($value),
            'from_email' => $this->setFromEmail($value),
            'to_email' => $this->setToEmail($value),
            'data' => $this->internalSetData($value),
            default => false
        };
    }

    /**
     * @param array $data
     * @return static
     */
    public function setData(array $data): static { return setData($this, $data); }

    /**
     * @param array $data
     * @return static
     */
    public function setEmpty(array $data): static { return setEmpty($this, $data); }

    public function getData(bool $echo = false): array
    {
        $array = [];
        if(!is_null($this->_uuid)) $array['uuid'] = $this->_uuid;
        if(!is_null($this->_from_uuid) && !$echo) $array['from_uuid'] = $this->_from_uuid;
        if(!is_null($this->_from_email)) $array['from_email'] = $this->_from_email;
        if(!is_null($this->_to_uuid) && !$echo) $array['to_uuid'] = $this->_to_uuid;
        if(!is_null($this->_to_email)) $array['to_email'] = $this->_to_email;
        return $array;
    }

    /**
     * @param int $id
     * @return static
     */
    public function setId(int $id): static { $this->_id = $id; return $this; }

    public function isEmpty(): bool { return (is_null($this->_uuid) && is_null($this->_from_uuid) && is_null($this->_to_uuid)); }

    protected function internalSetData(mixed $value): static
    {
        if(!is_array($value)) throw new InvalidPropertyOrMethod(sprintf(
                'Data value is from "%s" type and array type is required.', gettype($value)));
        return $this->setData($value);
    }

    public function setUuid(string $uuid): static
    {
        if(is_null($this->_uuid)) { $this->_uuid = $uuid; return $this; }
        throw new InvalidPropertyOrMethod('UUID property already defined.');
    }

    public function getUuid(): ?string { return $this->_uuid; }

    public function getFromUuid(): ?string { return $this->_from_uuid; }

    public function setFromUuid(mixed $value): static { $this->_from_uuid = $value; return $this; }

    public function getToUuid(): ?string { return $this->_to_uuid; }

    public function setToUuid(mixed $value): static { $this->_to_uuid = $value; return $this; }

    public function getFromEmail(): ?string { return $this->_from_email; }

    public function setFromEmail(mixed $value): static { $this->_from_email = $value; return $this; }

    public function getToEmail(): ?string { return $this->_to_email; }

    public function setToEmail(mixed $value): static { $this->_to_email = $value; return $this; }

}