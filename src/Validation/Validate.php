<?php

namespace DazzRick\HelloServer\Validation;

interface Validate
{

    public static function isCreationSchemaValid(array $data): bool;

}