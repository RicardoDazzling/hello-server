<?php

namespace DazzRick\HelloServer\Entity;

use Ramsey\Collection\Exception\InvalidPropertyOrMethod;

class User implements Entitable
{
    private ?int $_id;

    private string $_uuid;

    private bool $_status;

    private ?string $_name;

    private string $_email;

    private ?string $_default;

    private string $_creation_date;

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
        if($name === 'status')
        {
            return $this->getStatus();
        }
        if($name === 'name')
        {
            return $this->getName();
        }
        if($name === 'email')
        {
            return $this->getEmail();
        }
        if($name === 'default')
        {
            return $this->getDefault();
        }
        if($name === 'creation_date')
        {
            return $this->getCreationDate();
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
        if($name === 'status')
        {
            return $this->setStatus($value);
        }
        if($name === 'name')
        {
            return $this->setName($value);
        }
        if($name === 'email')
        {
            return $this->setEmail($value);
        }
        if($name === 'default')
        {
            return $this->setDefault($value);
        }
        if($name === 'creation_date')
        {
            return $this->setCreationDate($value);
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

    public function setId(int $id) :self
    {
        $this->_id = $id;
        return $this;
    }

    public function setUuid(string $uuid) :self
    {
        if(empty($this->_uuid))
        {
            $this->_uuid = $uuid;
            return $this;
        }
        throw new InvalidPropertyOrMethod('UUID property already defined.');
    }

    public function getUuid(): string
    {
        return $this->_uuid;
    }

    public function setStatus(bool $status) :self
    {
        $this->_status = $status;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->_status;
    }

    public function setName(string $name): self
    {
        $this->_name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->_name;
    }

    public function setEmail(string $email): self
    {
        $this->_email = $email;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->_email;
    }

    public function setDefault(string $default): self
    {
        $this->_default = $default;

        return $this;
    }

    public function getDefault(): ?string
    {
        return $this->_default;
    }

    public function setCreationDate(string $creation_date): self
    {
        $this->_creation_date = $creation_date;

        return $this;
    }

    public function getCreationDate(): string
    {
        return $this->_creation_date;
    }

    public function setData(array $data): self
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

    public function getData(): array
    {
        $array = [];
        if(!empty($this->_uuid))
        {
            $array['uuid'] = $this->_uuid;
        }
        if(!empty($this->_status))
        {
            $array['status'] = $this->_status;
        }
        if(!empty($this->_name))
        {
            $array['name'] = $this->_name;
        }
        if(!empty($this->_email))
        {
            $array['email'] = $this->_email;
        }
        if(!empty($this->_default))
        {
            $array['default'] = $this->_default;
        }
        if(!empty($this->_creation_date))
        {
            $array['creation_date'] = $this->_creation_date;
        }
        return $array;
    }

    public function isEmpty(): bool
    {
        if(empty($this->_uuid) && empty($this->_status) && empty($this->_name) && empty($this->_email)
            && empty($this->_default) && empty($this->_creation_date))
        {
            return true;
        }
        return false;
    }
}