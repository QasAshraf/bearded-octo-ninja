<?php
require_once("phpws/websocket.client.php");

/* Simple WebSockets client which does basic test to see if our WebSockets server is working ;) */
$input = "Hello World!";
$msg = WebSocketMessage::create($input);

$client = new WebSocket("ws://localhost:8080");
$client->open();
$client->sendMessage($msg);

?>