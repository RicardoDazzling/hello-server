<?php

namespace DazzRick\HelloServer\Services;

use DazzRick\HelloServer\DAL\GMessageDAL;

class GMessageService extends GBaseService
{
    public const TYPE = GMessageDAL::TABLE_NAME;
}