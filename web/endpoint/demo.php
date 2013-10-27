<?php
require_once("phpws/websocket.client.php");

/* Simple WebSockets client which does basic test to see if our WebSockets server is working ;) */
$input = json_encode( array(

            'operation' => 'GAME',

            'type' => 'NEW',

            'recipient' => 'server',

            'message' => 'newgame',

            'sender' => 'steph',

            'id' => 388542958

        ));
$msg = WebSocketMessage::create($input);

$client = new WebSocket("ws://localhost:8080");
$client->open();
$client->sendMessage($msg);

?>
