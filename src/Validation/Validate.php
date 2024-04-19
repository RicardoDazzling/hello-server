<?php

namespace DazzRick\HelloServer\Validation;

interface Validate
{

    public static function isCreationSchemaValid(array $data): bool|array;

    public static function isUpdateSchemaValid(array $data): bool|array;

}