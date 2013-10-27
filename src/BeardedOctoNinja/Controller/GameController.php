<?php
namespace BeardedOctoNinja\Controller;
/**
 * Created by PhpStorm.
 * User: steph
 * Date: 27/10/13
 * Time: 05:16
 */

class GameController {
    private $grid = null;
    public function startGame() {
        $this->grid = new BeardedOctoNinja\Model\Maze("The Crystal Maze", 21, 21);
        return $this->grid->get_grid();
    }

    public function processMessage($msg) {
        switch ($msg->type) {
            case 'NEW':
                return $this->processNEWMessage($msg);
                break;
        }
    }

    public function processNEWMessage($msg) {
        $request = explode($msg->message, ' ');
        $command = reset($request);
        switch ($command) {
            case 'newgame':
                return $this->startGame();
        }
    }
}