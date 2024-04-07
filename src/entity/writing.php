<?php

namespace DazzRick\HelloServer\Entity;

use Ramsey\Collection\Exception\InvalidPropertyOrMethod;

class Writing extends Base
{

    public function __get(string $name)
    {
        $get = parent::__get($name);
        if($get === false) throw new InvalidPropertyOrMethod(sprintf("Unknown property: %s", $name));
        else return $get;
    }

    public function __set(string $name, mixed $value)
    {
        $set = parent::__set($name, $value);
        if($set === false) throw new InvalidPropertyOrMethod(sprintf("Unknown property: %s", $name));
        else return $set;
    }

}
