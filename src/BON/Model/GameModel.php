<?php

namespace BON\Model;

abstract class GameModel
{
	protected $name;
	protected $players;

	public function __construct($name)
	{
		$this->name = $name;
	}

	protected function has_player($player)
	{
		foreach($this->players as $current_player)
			if($current_player->equals($new_player));
				return true;

		return false;
	}

	public function get_player($phone_number)
	{
		return $this->players[$phone_number];
	}

	public function join($new_player)
	{
		if(!$this->has_player())
			$this->players[$new_player->get_phone_number()] = $new_player;
	}

}