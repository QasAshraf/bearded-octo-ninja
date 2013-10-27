<?php
namespace BeardedOctoNinja\Controller;

class SMSController {

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