<?php
namespace BeardedOctoNinja\Controller;
use BeardedOctoNinja\Model\Player;

class PlayerController {

    public function handleRequest($request)
    {
        switch(strtolower($request['type']))
        {
            case 'new': // Draw new player sent to client
            return array(
                'operation' => 'PLAYER',
                'type' => 'new',
                'name' => 'PLAYA_NAME', // Replace with playa name or unique identifier eg. digits.
                'start_location' => 'SPAWN_CELL', // Replace with cell in which this new player is born
                'game' => 'GAME_NAME'); // Replace with name of game
            break;
            case 'move': // Move a player to a new location
            return array(
                'operation' => 'PLAYER',
                'type' => 'move',
                'name' => 'PLAYA_NAME', // Replace with playa name or unique identifier eg. digits.,
                'x' => 'NEW_X_LOCATION', // Replace with x co-ord of cell
                'y' => 'NEW_Y_LOCATION' // Replace with y co-ord of cell
            );
            case 'leave': // Player leaving the game :(
            return array(
                'operation' => 'PLAYER',
                'type' => 'leave',
                'name' => 'PLAYA_NAME' // Replace with playa name or unique identifier eg. digits.
            );
            case 'chat': // Send a chat message (NOT FOR IMPLEMENTATION IN FR1)
            return array(
                'operation' => 'PLAYER',
                'type' => 'chat',
                'recipient' => 'ALL', // replace with recipient eg. default ALL
                'message' => 'CHAT_MESSAGE' // replace with chat message
            );
            default:
                return NULL; // Stub
                break;
        }
    }
}