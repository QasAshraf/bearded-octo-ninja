<?php

namespace BeardedOctoNinja\Model;

class Maze extends Game
{
	protected $grid;
	protected $start;
	protected $end;

	protected $players;

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

		$end = null;

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

				$this->end = $curr;

			}

		}

		$grid[$this->start[0]][$this->start[1]] = "s";
		$grid[$this->end[0]][$this->end[1]] = "e";
		$this->grid = $grid;
	}

	protected function get_pos(Player $player)
	{
		return $this->players[$player->get_phone_number()]->get_position();
	}

	public function join(Player $player)
	{
		if(isset($this->players[$player->get_phone_number()]))
			return array();

		parent::join($player);
		$this->players[$player->get_phone_number()]->set_position($this->start);
		return array("operation" => "PLAYER",
								 "type" => "join",
								 "name" => $player->get_user_name(),
								 "player" => $player->get_phone_number(),
								 "x" => $this->start[0],
								 "y" => $this->start[1]
								);
	}

	public function move($phone_number, $direction, $times)
	{
		$player = $this->players[$phone_number];
		$current_position = $this->get_pos($player);
		$new_pos = $current_position;

		switch($direction)
		{
			case "up":
				for($i = $current_position[1]; $i >= ($current_position[1] - $times); $i--)
				{
					if($this->grid[$new_pos[0]][$i] == "#")
					{
						break;
					}
					else if($this->grid[$new_pos[0]][$i] == "e")
					{
						return array("operation" => "PLAYER",
									 "type" => "win",
									 "name" => $player->get_user_name()
									);
					}
					else
					{
						$new_pos = array($new_pos[0], $i);
					}
				}
				break;
			case "right":
				for($i = $current_position[0]; $i <= $current_position[0] + $times; $i++)
				{
					if($this->grid[$i][$new_pos[1]] == "#")
					{
						break;
					}
					else if($this->grid[$i][$new_pos[1]] == "e")
					{
						return array("operation" => "PLAYER",
									 "type" => "win",
									 "name" => $player->get_user_name()
									);
					}
					else
					{
						$new_pos = array($i, $new_pos[1]);
					}
				}
				break;
			case "down":
				for($i = $current_position[1]; $i <= $current_position[1] + $times; $i++)
				{
					if($this->grid[$new_pos[0]][$i] == "#")
					{
						break;
					}
					else if($this->grid[$new_pos[0]][$i] == "e")
					{
						return array("operation" => "PLAYER",
									 "type" => "win",
									 "name" => $player->get_user_name()
									);
					}
					else
					{
						$new_pos = array($new_pos[0], $i);
					}
				}
				break;
			case "left":
				for($i = $current_position[0]; $i >= $current_position[0] - $times; $i--)
				{
					if($this->grid[$i][$new_pos[1]] == "#")
					{
						break;
					}
					else if($this->grid[$i][$new_pos[1]] == "e")
					{
						return array("operation" => "PLAYER",
									 "type" => "win",
									 "name" => $player->get_user_name()
									);
					}
					else
					{
						$new_pos = array($i, $new_pos[1]);
					}
				}
				break;
			default:
				return array();
		}

		return array("operation" => "PLAYER",
				     "type" => "move",
					 "player" => $player->get_phone_number(),
					 "x" => $new_pos[0],
					 "y" => $new_pos[1]
					);
	}

	public function say($player, $message)
	{
        // TODO: Implement.
	}

	public function leave($phone_number)
	{
        $player = $this->players[$phone_number];
        unset($this->players[$phone_number]);

        return array("operation" => "PLAYER",
					 "type" => "leave",
					 "name" => $player->get_user_name()
					);
	}

	public function get_start()
	{
		return $this->start;
	}

	public function get_end()
	{
		return $this->end;
	}

	public function get_grid()
	{
		return $this->grid;
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