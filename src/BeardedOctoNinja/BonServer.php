<?php
namespace BeardedOctoNinja;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use BeardedOctoNinja\Controller\GameController;

class BonServer implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->game = new GameController();
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;


        // TODO: Parse message, if type = SMS then we can do something useful.
        $request = json_decode($msg, 1);
        $response = null;
        switch($request['operation']) {
            case 'SMS':
                break;
            case 'GAME':
                $response = $this->game->processMessage($request);
                break;
        }

        if($response !== null){
            echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
                , $from->resourceId, json_encode($response), $numRecv, $numRecv == 1 ? '' : 's');
            foreach ($this->clients as $client) {
                if ($from !== $client) {
                    // The sender is not the receiver, send to each client connected
                    $client->send(json_encode($response));
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

} 