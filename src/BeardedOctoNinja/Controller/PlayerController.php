<?php
namespace BeardedOctoNinja\Controller;
use BeardedOctoNinja\Model\Player;

class PlayerController {

    public function handleRequest($request)
    {
        switch(strtolower($request['type']))
        {
            default:
                return NULL; // Stub
                break;
        }
    }
}