<?php
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use BeardedOctoNinja\BonServer;

require dirname(__DIR__) . '/../vendor/autoload.php';

/**
 *  Spawns off a beardedOctoNinjaServer which sits on port 8080 waiting for events to happen. It then uses it's crazy
 *  ninja-like abilities combined with the wisdom of a 8 (yes eight!) bearded servers to process your request.
 */

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new BonServer()
        )
    ),
    8080
);

$server->run();