#!/usr/bin/env php
<?php
chdir(dirname(__FILE__));
require_once('autoload.php');

$server = new WebSocketServer\WebSocket("0.0.0.0","8081");

try {
    $server->run();
} catch (Exception $e) {
    $server->stdout($e->getMessage());
}