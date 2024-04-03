<?php

namespace DazzRick\HelloServer\Validation;

interface Validation
{

    public static function isCreationSchemaValid(array $data): bool;

}