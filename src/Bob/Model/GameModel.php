<?php

namespace Bob\Model;

abstract class GameModel
{
	protected $name;
	protected $players;

	public function __construct($name)
	{
		$this->name = $name;
	}

	public function join($player)
	{
		$this->players[] = $player;
	}

}

class MazeModel extends GameModel
{
	$maze;
}