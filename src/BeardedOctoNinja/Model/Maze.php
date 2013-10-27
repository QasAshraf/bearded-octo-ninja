<?php

namespace BeardedOctoNinja\Model;

class Maze extends Game
{
	protected $grid;
	protected $start;
	protected $end;

	protected $player_positions;

	public function __construct($game_name, $x_size, $y_size)
	{
		parent::__construct($game_name);

		// Initilize some vars [x]
		$wall_list = array();
		$grid = array();

		// Generate a 2d array of # with given sizes [x]
		for($i = 0; $i < $x_size; $i++)
			for($j = 0; $j < $y_size; $j++)
				$grid[$i][$j] = "#";

		// Take a random side wall [x]
		$side = rand(1,4);
		if($side == 1)
			$this->start = array(rand(1, $x_size-1), 1);
		else if($side == 2)
			$this->start = array($x_size-1, rand(1, $y_size-1));
		else if($side == 3)
			$this->start = array(rand(1, $x_size-1), $y_size-1);
		else if($side == 4)
			$this->start = array(1, rand(1, $y_size-1));

		// Mark this random side wall as the starting point, and set as passage [x]
		$current = $this->start;
		$grid[$current[0]][$current[1]] = ".";

		// If adjacent walls do not fall off the edge of the grid, add them to list of walls to be checked
		if($current[0]+1 < $x_size)
			$wall_list[] = array($current[0]+1, $current[1]);

		if($current[0]-1 >= 0)
			$wall_list[] = array($current[0]-1, $current[1]);

		if($current[1]+1 < $y_size)
			$wall_list[] = array($current[0], $current[1]+1);

		if($current[1]-1 >= 0)
			$wall_list[] = array($current[0], $current[1]-1);

		while(sizeof($wall_list) > 0)
		{
			// Pop a random wall off the wall list
			shuffle($wall_list);
			$curr = $wall_list[0];
			unset($wall_list[0]);

			$adjacent_spaces = 0;
			$potential_spaces = array();

			if($curr[0]+1 < $x_size)
			{
				if($grid[$curr[0]+1][$curr[1]] == ".")
					$adjacent_spaces++;
				else
					$potential_spaces[] = array($curr[0]+1, $curr[1]);
			}

			if($curr[0]-1 >= 0)
			{
				if($grid[$curr[0]-1][$curr[1]] == ".")
					$adjacent_spaces++;
				else
					$potential_spaces[] = array($curr[0]-1, $curr[1]);
			}

			if($curr[1]+1 < $y_size)
			{
				if($grid[$curr[0]][$curr[1]+1] == ".")
					$adjacent_spaces++;
				else
					$potential_spaces[] = array($curr[0], $curr[1]+1);
			}

			if($curr[1]-1 >= 0)
			{
				if($grid[$curr[0]][$curr[1]-1] == ".")
					$adjacent_spaces++;
				else
					$potential_spaces[] = array($curr[0], $curr[1]-1);
			}

			if($adjacent_spaces == 1)
			{
				$grid[$curr[0]][$curr[1]] = ".";

				foreach($potential_spaces as $space)
				{
					$found = false;
					foreach($wall_list as $wall)
						if($wall == $space)
							$found = true;

					if(!$found)
						$wall_list[] = $space;
				}

			}

			if(sizeof($wall_list) == 1)
				$this->end = $curr;

		}

		$grid[$this->start[0]][$this->start[1]] = "s";
		$grid[$this->end[0]][$this->end[1]] = "e";

		$this->grid = $grid;
	}

	protected function get_pos(Player $player)
	{
		return $this->player_positions[$player->get_phone_number()];
	}

	public function join(Player $player)
	{
		parent::join($player);
		$this->player_positions[$this->player->get_phone_number()] = $this->start;
	}

	public function move(Player $player, $direction, $times)
	{
		switch($direction)
		{
			case "up":
			case "u":
			case "north":
			case "n":
				$direction = 1;
				break;
			case "right":
			case "r":
			case "east":
			case "e":
				$direction = 2;
				break;
			case "down":
			case "d":
			case "south":
			case "s":
				$direction = 3;
				break;
			case "left":
			case "l":
			case "west":
			case "w":
				$direction = 4;
				break;
			default:
				return array();
		}

		$current_position = $this->get_pos($player);
		$new_pos = $current_position;

		$moved = 0;
		if($direction %2 == 1)
			for($i = $current_position[1]; $i <= $times; $i += -($direction-2))
			{
				if($this->grid[$current_position[0]][$i] == "#")
					break;
				else if($this->grid[$current_position[0]][$i] == "e")
				{
					return array("Win" => $player->get_user_name());
				}
				else
					$new_pos = array($current_position[0], $i);
			}
		else
			for($i = $current_position[0]; $i <= $times; $i += -($direction-3))
			{
				if($this->grid[$i][$current_position[1]] == "#")
					break;
				else if($this->grid[$i][$current_position[1]] == "e")
				{
					return array("Win" => $player->get_user_name());
				}
				else
					$new_pos = array($i, $current_position[1]);
			}

		return array("player" => $player->get_phone_number(),
					 "x" => $new_pos[0],
					 "y" => $new_pos[1]
					);
	}

	public function say($player, $message)
	{
        // TODO: Implement.
	}

	public function leave($player)
	{
        // TODO: Something.
	}

	public function get_start()
	{
		return $this->start; // TODO: Implement
	}

	public function get_end()
	{
		return $this->end; // TODO: Implement
	}

	public function get_grid()
	{
		return $this->grid; // TODO: Implement
	}

	public function print_grid()
	{
		$string = "<h1>Grid:</h1><p style=\"font-family:courier;\">";
		for($i = 0; $i < sizeof($this->grid); $i++)
		{
			for($j = 0; $j < sizeof($this->grid[$i]); $j++)
				$string .= $this->grid[$i][$j];
			$string .='<br />';
		}
		return $string."</p>";
	}
}