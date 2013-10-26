<?php

namespace Bob\Model;

class Player
{
	protected $phone_number;
	protected $user_name;

	public function __construct($phone_number, $user_name)
	{
		$this->phone_number = $phone_number;
		$this->user_name = $user_name;
	}

	public function join($game_name)
	{
		return $game_name->join($this);
	}

	public function move($direction, $times = 1)
	{
		return move($direction, $times);
	}

	public function chat($message)
	{
		return send($message);
	}
}
