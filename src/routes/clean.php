<?php
namespace DazzRick\HelloServer;

use DazzRick\HelloServer\Exceptions\BadRequestException;
use DazzRick\HelloServer\Services\FileService;
use DazzRick\HelloServer\Services\MessageService;

function getResponse(): string
{
    $table = $_REQUEST['table'] ?? null;
    if(is_null($table)) throw new BadRequestException('Argument required: "table"');
    if(!in_array($table, ['message', 'file'], TRUE)) throw new BadRequestException('Unknown table');
    $service = $table==='message'?new MessageService(): new FileService();
    $service->clean();
}
