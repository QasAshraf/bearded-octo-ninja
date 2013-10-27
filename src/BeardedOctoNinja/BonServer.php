<?php
namespace BeardedOctoNinja;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use BeardedOctoNinja\Controller\GameController;
use BeardedOctoNinja\Controller\SMSController;
use BeardedOctoNinja\Controller\PlayerController;

class BonServer implements MessageComponentInterface
{
    protected $clients;

    protected $sms, $game, $player; // Controllers

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    private function getSMSController() // No need to create lots of objects
    {
        if(is_null($this->sms))
        {
            return new SMSController();
        }
        return $this->sms;
    }

    private function getPlayerController()
    {
        if(is_null($this->player))
        {
            return new PlayerController();
        }
        return $this->player;
    }

    private function getGameController()
    {
        if(is_null($this->game))
            $this->game = new GameController();
        return $this->game;
    }

    protected function handleSMS($request)
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

                $command = explode(" ", $request['message'], 2);
                switch($command[0])
                {
                    case "join":
                        return $this->getGameController()->new_player($request['sender'], $command[1]);
                        break;
                    case "move":
                        $args = explode(" ", $command[1], 2);
                        return $this->getGameController()->move_player($request['sender'], $args[0], $args[1]);
                        break;
                    case "leave":
                        return $this->getGameController()->leave_player($request['sender']);
                        break;
                    default:
                        return NULL;
                        break;
                }
                break;
            default:
                return NULL;
                break;
        }
       
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $request = json_decode($msg, 1); // Force into array
        $response = null;
        switch (strtolower($request['operation'])) {
            case 'sms':
                $response = $this->handleSMS($request);
                break;
            case 'player':
                $this->player = $this->getPlayerController();
                $response = $this->player->handleRequest($request);
                break;
            case 'game':
                $this->game = $this->getGameController();
                $response = $this->game->processMessage($request);
                break;
            default:
                $response = NULL;
                break;
        }

        if (is_null($response)) {
            $response = array('code' => 400,
                'status' => 'Bad request. Either the request format is invalid or the server doesn\'t
                                           know how to deal with it, yet');

        }

        echo sprintf("\n" . 'Sending message "%s" to all other connection %s' . "\n"
            , $from->resourceId, json_encode($response));

        foreach ($this->clients as $client) {
            $client->send(json_encode($response)); // Could be optimised to send only to select clients at some point
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

} 
