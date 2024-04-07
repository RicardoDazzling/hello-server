<?php

namespace DazzRick\HelloServer\Entity;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use RuntimeException;
use Ramsey\Collection\Exception\InvalidPropertyOrMethod;

class Token implements Entitable
{
    protected ?int $_id = null;

    protected ?string $_token = null;

    protected ?string $_uuid = null;

    protected ?string $_email = null;

    protected ?string $_name = null;

    protected ?int $_time = null;

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
        if(empty($this->_token)) throw new RuntimeException('Token is not defined');
    }

    private function readonly(string $name): void
    {
        throw new InvalidPropertyOrMethod(sprintf('%s is a read-only property.', $name));
    }

    public function __get(string $name)
    {
        return match($name)
        {
            'id' => $this->_id,
            'token' => $this->getToken(),
            'time' => $this->getTime(),
            'uuid' => $this->getUuid(),
            'email' => $this->getEmail(),
            'name' => $this->getName(),
            'data' => $this->getData(),
            default => throw new InvalidPropertyOrMethod(sprintf("Unknown property: %s", $name))
        };
    }

    public function __set(string $name, mixed $value)
    {
        return match($name)
        {
            'id' => $this->setId($value),
            'token' => $this->setToken($value),
            'time' => $this->setTime($value),
            'uuid' => $this->setUuid($value),
            'email' => $this->setEmail($value),
            'name' => $this->setName($value),
            'data' => $this->setData($value),
            default => throw new InvalidPropertyOrMethod(sprintf("Unknown property: %s", $name))
        };
    }

    public function setId(int $id): static { $this->readonly('ID'); }

    public function setToken(string $token): static
    {
        $this->_token = $token;
        $payload = /*(array)*/JWT::decode($token, new Key($_ENV['JWT_KEY'], $_ENV['JWT_ALG']));
        $this->_time = $payload->iat;
        $this->_uuid = $payload->data['data'];
        $this->_name = $payload->data['name'];
        $this->_email = $payload->data['email'];
        return $this;
    }

    public function getToken(): ?string { return $this->_token; }

    public function setUuid(string $uuid): static { $this->readonly('UUID'); }

    public function getUuid(): ?string { $this->tokenAlreadyDefined(); return $this->_uuid; }

    public function setTime(int $time): static { $this->readonly('Time'); }

    public function getTime(): ?int { $this->tokenAlreadyDefined(); return $this->_time; }

    public function setEmail(string $email): static { $this->readonly('EMail'); }

    public function getEmail(): ?string { $this->tokenAlreadyDefined(); return $this->_email; }

    public function setName(string $name): static { $this->readonly('Name'); }

    public function getName(): ?string { $this->tokenAlreadyDefined(); return $this->_name; }

    public function setData(array $data): static { return setData($this, $data); }

    public function getData(): array
    {
        $array = [];
        if(!empty($this->_uuid)) $array['uuid'] = $this->_uuid;
        if(!empty($this->_token)) $array['token'] = $this->_token;
        if(!empty($this->_time)) $array['time'] = $this->_time;
        if(!empty($this->_email)) $array['email'] = $this->_email;
        if(!empty($this->_name)) $array['name'] = $this->_name;
        return $array;
    }

    public function isEmpty(): bool { return empty($this->_token); }
}