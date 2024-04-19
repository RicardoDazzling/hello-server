<?php
// miscellaneous file (MISC)

function response($data): void
{
    header('Content-Type: application/json');
    echo json_encode($data);
}