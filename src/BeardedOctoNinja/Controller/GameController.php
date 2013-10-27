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
    public function startGame() {
        $this->maze = new Maze("The Crystal Maze", 21, 21);
        return $this->maze->get_grid();
    }

    public function processMessage($msg) {
        switch ($msg->type) {
            case 'NEW':
                return $this->processNEWMessage($msg);
                break;
        }
        return 'noendpoint';
    }

    public function processNEWMessage($msg) {
        switch ($msg->message) {
            case 'newgame':
                return $this->startGame();
        }
        return 'noendpoint';
    }
}