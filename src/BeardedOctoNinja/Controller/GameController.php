<?php
namespace BeardedOctoNinja\Controller;
use BeardedOctoNinja\Model\Maze;
/**
 * Created by PhpStorm.
 * User: steph
 * Date: 27/10/13
 * Time: 05:16
 */

class GameController {

    private $maze = null;
    private $size = 21;
    private $name = null;

    public function startGame() {
        $this->maze = new Maze($this->name, $this->size, $this->size);
    }

    public function processMessage($msg) 
    {

        switch ($msg['type']) {
            case 'create': // Client requested a new game -- let's set one up and send them the details back :)
                $this->name = $msg['message'];
                $this->size = $msg['size'];
                $this->startGame();
                $response = array(
                    'operation' => 'GAME',
                    'type' => 'new',
                    'recipient' => 'ALL',
                    'grid' => $this->maze->get_grid(),
                    'size' => $this->size,
                    'name' => $this->name
                );
                return $response;
                break;
            case 'end':
                return array(
                    'operation' => 'GAME',
                    'type' => 'end',
                    'name' => 'GAME_NAME', // Name of the game you wanna stops
                    'reason' => 'REASON' // Reason for stopping it, best be a good one!
                );
                break;
            default:
                return NULL;
                break;
        }
    }

    public function new_player($phone_number, $name)
    {
        $player = new Player($phone_number, $name);
        return $this->maze->join($player);
    }

    public function move_player($phone_number, $direction, $times)
    {
        return $this->maze->move($phone_number, $direction, $times);
    }

    public function leave_player($phone_number)
    {
        return $this->maze->leave($phone_number);
    }
}