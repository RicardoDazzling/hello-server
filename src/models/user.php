<?php

namespace DazzRick\HelloServer\Models;

class User
{
    public function __construct(
        public readonly string $uuid,
        public readonly string $name,
        public readonly string | null $phone,
        public readonly string | null $email
    ){}

    private function get_array(bool $return_uuid = true): array
    {
        $my_array = ['name'=>$this->name,
            'phone'=>$this->phone,
            'email'=>$this->email];
        if($return_uuid && !empty($this->uuid)) $my_array+=['uuid'=>$this->uuid];
        return $my_array;
    }
}