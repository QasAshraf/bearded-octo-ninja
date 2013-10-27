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

    public function processMessage($msg) {
        switch ($msg->type) {
            case 'create':
                $this->name = $msg->message;
                $this->size = $msg->size;
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
        }
        return 'noendpoint';
    }
}