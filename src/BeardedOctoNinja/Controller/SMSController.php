<?php
namespace BeardedOctoNinja\Controller;

class SMSController {

    public function handleRequest($request)
    {
        switch(strtolower($request['type']))
        {
            case 'outgoing':
                return array(
                    'operation' => 'SMS',
                    'type' => 'outgoing',
                    'recipient' => $request['to'],
                    'message' => $request['content'],
                    'sender' => $request['from']
                ); // TODO: Maybe change this format, depending on what's easier for SMS Interceptor
                break;
            case 'incoming':
                return array(
                  'operation' => 'PLAYER',
                  'type' => 'move',
                  'name' => $request['from'],
                  'x' => 2,
                  'y' => 0
                ); // TODO: Process either player movement or...process joining/leaving game
                break;
            default:
                return NULL;
                break;
        }
    }
}