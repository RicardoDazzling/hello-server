<?php

namespace DazzRick\HelloServer\Services;

use DazzRick\HelloServer\DAL\GFileDAL;

class GFileService extends GBaseService
{
    public const TYPE = GFileDAL::TABLE_NAME;
}