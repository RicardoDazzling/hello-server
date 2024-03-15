<?php
namespace DazzRick\HelloServer;

(new AllowCors)->init();
header('content-type: application/json');