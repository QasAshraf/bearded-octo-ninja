<?php

namespace BON;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Model\MazeModel;
use Model\PlayerModel;

class Chat implements MessageComponentInterface {

    protected $clients;
    protected $games;
    protected $players;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $msg = explode(" ", $msg, 2);
        if($msg[0] == "NEW_GAME")
        {
            $name = $msg[1];
            $this->games[$name] = new MazeModel($name, 20, 20);
            $from->send(json_encode(array("id" => $name, $this->games[$id])));
        }


        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        foreach ($this->clients as $client) {
            if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                $client->send($msg);
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

    public function onTextReceived($entry) {
        $entryData = strtolower(json_decode($entry, true));

        $message = explode(" ", $entryData['content'], 2);

        switch($message[0])
        {

            case "join":
                $args = explode(" ", $message[1]);
                $players[$entryData['from']] = new PlayerModel($entryData['from'], $args[2]);
                $games[$args[1]]->join($players[$entryData['from']]);
                $players[$entryData['from']]->join($args[1]);
                break;
            case "move":
                $args = explode(" ", $message[1]);
                $games[$players[$entryData['from']]->get_current_game()]->move($players[$entryData['from']], $args[0], $args[1]);
                break;
            case "leave":
                break;
            case "say";
                break;
            default:
                break;
        }

        echo sprintf('Text received from "%s" with message "%s" ' . "\n"
            , $entryData['from'], $entryData['content']);
    }
}