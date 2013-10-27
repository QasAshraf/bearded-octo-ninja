<?php

namespace BeardedOctoNinja\Model;

abstract class Game
{
	protected $name;
	protected $players;

	public function __construct($name)
	{
		$this->name = $name;
	}

	protected function has_player(Player $player)
	{
		if(isset($this->players[$player->get_phone_number()]))
			return true;
		return false;
	}

	public function get_player($phone_number)
	{
		return $this->players[$phone_number];
	}

	public function join(Player $player)
	{
		if(!$this->has_player($player))
			$this->players[$player->get_phone_number()] = $player;
		
		$this->players[$player->get_phone_number()]->join(this);
	}

}