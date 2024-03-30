<?php

namespace DazzRick\HelloServer\Entity;

use Override;
use Ramsey\Collection\Exception\InvalidPropertyOrMethod;

class Verification implements Entitable
{
    private ?int $_id = null;

    private ?string $_uuid = null;

    private ?string $_code = null;

    private ?int $_last_try = null;

    private ?int $_try_number = null;

    public function __get(string $name)
    {
        if($name === 'id')
        {
            return $this->_id;
        }
        if($name === 'uuid')
        {
            return $this->getUuid();
        }
        if($name === 'code')
        {
            return $this->getCode();
        }
        if($name === 'last_try')
        {
            return $this->getLastTry();
        }
        if($name === 'try_number')
        {
            return $this->getTryNumber();
        }
        if($name === 'data')
        {
            return $this->getData();
        }
        throw new InvalidPropertyOrMethod(sprintf("Unknown property: %s", $name));
    }

    public function __set(string $name, mixed $value)
    {
        if($name === 'id')
        {
            return $this->setId($value);
        }
        if($name === 'uuid')
        {
            return $this->setUuid($value);
        }
        if($name === 'code')
        {
            return $this->setCode($value);
        }
        if($name === 'last_try')
        {
            return $this->setLastTry($value);
        }
        if($name === 'try_number')
        {
            return $this->setTryNumber($value);
        }
        if($name === 'data')
        {
            if(!is_array($value))
            {
                throw new InvalidPropertyOrMethod(sprintf(
                    'Data value is from "%s" type and array type is required.', gettype($value)));
            }
            return $this->setData($value);
        }
        throw new InvalidPropertyOrMethod(sprintf("Unknown property: %s", $name));
    }

    #[Override] public function setId(int $id): self
    {
        $this->_id = $id;
        return $this;
    }

    #[Override] public function setUuid(string $uuid): self
    {
        if(empty($this->_uuid))
        {
            $this->_uuid = $uuid;
            return $this;
        }
        throw new InvalidPropertyOrMethod('UUID property already defined.');
    }

    #[Override] public function getUuid(): string
    {
        return $this->_uuid;
    }

    #[Override] public function setData(array $data): self
    {
        if(count($data) > 0)
        {
            foreach ($data as $data_name => $data_value)
            {
                $this->__set($data_name, $data_value);
            }
        }
        return $this;
    }

    #[Override] public function getData(): array
    {
        $array = [];
        if(!empty($this->_uuid))
        {
            $array['uuid'] = $this->_uuid;
        }
        if(!empty($this->_code))
        {
            $array['code'] = $this->_code;
        }
        if(!empty($this->_last_try))
        {
            $array['last_try'] = $this->_last_try;
        }
        if(!empty($this->_try_number))
        {
            $array['email'] = $this->_try_number;
        }
        return $array;
    }

    #[Override] public function isEmpty(): bool
    {
        if(empty($this->_uuid) && empty($this->_code) && empty($this->_last_try) && empty($this->_try_number))
        {
            return true;
        }
        return false;
    }

    public function getCode(): ?string
    {
        return $this->_code;
    }

    public function setCode(mixed $code): self
    {
        $this->_code = $code;
        return $this;
    }

    public function getLastTry(): ?int
    {
        return $this->_last_try;
    }

    public function setLastTry(mixed $last_try): self
    {
        $this->_last_try = $last_try;
        return $this;
    }

    public function getTryNumber(): ?int
    {
        return $this->_try_number;
    }

    public function setTryNumber(mixed $value): self
    {
        $this->_try_number = $value;
        return $this;
    }
}