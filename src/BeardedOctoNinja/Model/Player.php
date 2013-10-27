<?php

namespace BeardedOctoNinja\Model;

class Player
{
	protected $phone_number;
	protected $user_name;

	protected $current_game;

	public function __construct($phone_number, $user_name)
	{
		$this->phone_number = $phone_number;
		$this->user_name = $user_name;
	}

	public function get_current_game()
	{
		return $this->current_game;
	}

	public function join($game_name)
	{
		$this->current_game = $game_name;	
	}

	public function move($direction, $times = 1)
	{
		return $this->current_game->move(this, $direction, $times); // TODO: Implement
	}

	public function chat($message)
	{
		return $this->current_game->say(this, $message); // TODO: Implement
	}

	public function leave()
	{
		$this->current_game->leave(this); // TODO: Implement
	}

	public function equals(Player $player)
	{
		if(get_class($player) == get_class($this))
			if($player->get_phone_number() == $this->get_phone_number())
				return true;

		return false;

	}

	public function get_phone_number()
	{
		return $this->phone_number;
	}

	public function get_user_name()
	{
		return $this->user_name;
	}
}
