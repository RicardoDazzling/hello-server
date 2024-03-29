<?php

namespace DazzRick\HelloServer\Entity;

use Override;
use Ramsey\Collection\Exception\InvalidPropertyOrMethod;

class Message implements Entitable
{

    private ?int $_id;

    private string $_uuid;

    private string $_from;

    private string $_to;

    private string $_content;

    private int $_send;

    private int $_received;

    private int $_read;

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
        if($name === 'from')
        {
            return $this->getFrom();
        }
        if($name === 'to')
        {
            return $this->getTo();
        }
        if($name === 'content')
        {
            return $this->getContent();
        }
        if($name === 'send')
        {
            return $this->getSend();
        }
        if($name === 'received')
        {
            return $this->getReceived();
        }
        if($name === 'read')
        {
            return $this->getRead();
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
        if($name === 'from')
        {
            return $this->setFrom($value);
        }
        if($name === 'to')
        {
            return $this->setTo($value);
        }
        if($name === 'content')
        {
            return $this->setContent($value);
        }
        if($name === 'send')
        {
            return $this->setSend($value);
        }
        if($name === 'received')
        {
            return $this->setReceived($value);
        }
        if($name === 'read')
        {
            return $this->setRead($value);
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
        if(!empty($this->_from))
        {
            $array['from'] = $this->_from;
        }
        if(!empty($this->_to))
        {
            $array['to'] = $this->_to;
        }
        if(!empty($this->_content))
        {
            $array['content'] = $this->_content;
        }
        if(!empty($this->_send))
        {
            $array['send'] = $this->_send;
        }
        if(!empty($this->_received))
        {
            $array['received'] = $this->_received;
        }
        if(!empty($this->_read))
        {
            $array['read'] = $this->_read;
        }
        return $array;
    }

    #[Override] public function isEmpty(): bool
    {
        if(empty($this->_uuid) && empty($this->_from) && empty($this->_to) && empty($this->_content)
            && empty($this->_send))
        {
            return true;
        }
        return false;
    }

    public function getFrom(): string
    {
        return $this->_from;
    }

    public function setFrom(mixed $value): self
    {
        $this->_from = $value;
        return $this;
    }

    public function getTo(): string
    {
        return $this->_to;
    }

    public function setTo(mixed $value): self
    {
        $this->_to = $value;
        return $this;
    }

    public function getContent(): string
    {
        return $this->_content;
    }

    public function setContent(mixed $value): self
    {
        $this->_content = $value;
        return $this;
    }

    public function getSend(): int
    {
        return $this->_send;
    }

    public function setSend(mixed $value): self
    {
        $this->_send = $value;
        return $this;
    }

    public function getReceived(): int
    {
        return $this->_received;
    }

    public function setReceived(mixed $value): self
    {
        $this->_received = $value;
        return $this;
    }

    public function getRead(): int
    {
        return $this->_read;
    }

    public function setRead(mixed $value): self
    {
        $this->_read = $value;
        return $this;
    }
}