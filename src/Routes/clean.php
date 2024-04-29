<?php

use DazzRick\HelloServer\Exceptions\BadRequestException;
use DazzRick\HelloServer\Services\FileService;
use DazzRick\HelloServer\Services\GFileService;
use DazzRick\HelloServer\Services\GMessageService;
use DazzRick\HelloServer\Services\LostService;
use DazzRick\HelloServer\Services\MessageService;

function getResponse(): string
{
    $table = empty($_REQUEST['subresource']) ? null : $_REQUEST['subresource'];
    if(is_null($table)) throw new BadRequestException('Argument required: "table"');
    $service = match ($table) {
        'message' => new MessageService(),
        'file' => new FileService(),
        'gmessage' => new GMessageService(),
        'gfile' => new GFileService(),
        'lost' => new LostService(),
        default => throw new BadRequestException('Unknown table')
    };
    $service->clean();
    return '[true]';
}

echo getResponse();
