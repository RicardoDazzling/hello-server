<?php

namespace DazzRick\HelloServer\Entity;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Override;
use RuntimeException;
use Ramsey\Collection\Exception\InvalidPropertyOrMethod;

class Token implements Entitable
{
    private int $_id;

    private string $_token;

    private string $_uuid;

    private string $_email;

    private string $_name;

    private int $_time;

    public static function validate(string $token): bool
    {
        try
        {
            JWT::decode($token, new Key($_ENV['JWT_KEY'], $_ENV['JWT_ALG']));
            return true;
        }
        catch (\Exception)
        {
            return false;
        }
    }

    private function tokenAlreadyDefined(): void
    {
        if(empty($this->_token))
        {
            throw new RuntimeException('Token is not defined');
        }
    }

    private function readonly(string $name): void
    {
        throw new InvalidPropertyOrMethod(sprintf('%s is a read-only property.', $name));
    }

    public function __get(string $name)
    {
        if($name === 'id')
        {
            return $this->_id;
        }
        if($name === 'token')
        {
            return $this->getToken();
        }
        if($name === 'time')
        {
            return $this->getTime();
        }
        if($name === 'uuid')
        {
            return $this->getUuid();
        }
        if($name === 'email')
        {
            return $this->getEmail();
        }
        if($name === 'name')
        {
            return $this->getName();
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
            $this->readonly('ID');
        }
        if($name === 'token')
        {
            return $this->setToken($value);
        }
        if($name === 'time')
        {
            return $this->setTime($value);
        }
        if($name === 'uuid')
        {
            return $this->setUuid($value);
        }
        if($name === 'email')
        {
            return $this->setEmail($value);
        }
        if($name === 'name')
        {
            return $this->setName($value);
        }
        if($name === 'data')
        {
            return $this->setData($value);
        }
        throw new InvalidPropertyOrMethod(sprintf("Unknown property: %s", $name));
    }

    public function setId(int $id): self
    {
        $this->_id = $id;
        return $this;
    }

    public function setToken(string $token): self
    {
        $this->_token = $token;
        $payload = /*(array)*/JWT::decode($token, new Key($_ENV['JWT_KEY'], $_ENV['JWT_ALG']));
        $this->_time = $payload->iat;
        $this->_uuid = $payload->data['data'];
        $this->_name = $payload->data['name'];
        $this->_email = $payload->data['email'];
        return $this;
    }

    public function getToken(): string
    {
        return $this->_token;
    }

    #[Override] public function setUuid(string $uuid): self
    {
        $this->readonly('UUID');
    }

    #[Override] public function getUuid(): string
    {
        $this->tokenAlreadyDefined();
        return $this->_uuid;
    }

    public function setTime(int $time): self
    {
        $this->readonly('Time');
    }

    public function getTime(): int
    {
        $this->tokenAlreadyDefined();
        return $this->_time;
    }

    public function setEmail(string $email): self
    {
        $this->readonly('EMail');
    }

    public function getEmail(): string
    {
        $this->tokenAlreadyDefined();
        return $this->_email;
    }

    public function setName(string $name): self
    {
        $this->readonly('Name');
    }

    public function getName(): string
    {
        $this->tokenAlreadyDefined();
        return $this->_name;
    }

    #[Override] public function setData(array $data): Entitable
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
        if(!empty($this->_token))
        {
            $array['token'] = $this->_token;
        }
        if(!empty($this->_time))
        {
            $array['time'] = $this->_time;
        }
        if(!empty($this->_email))
        {
            $array['email'] = $this->_email;
        }
        if(!empty($this->_name))
        {
            $array['name'] = $this->_name;
        }
        return $array;
    }

    #[Override] public function isEmpty(): bool
    {
        if(empty($this->_token))
        {
            return true;
        }
        return false;
    }
}