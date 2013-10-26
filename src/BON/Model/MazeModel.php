<?php

namespace BON\Model;

class MazeModel extends GameModel
{
	protected $grid;
	protected $start;
	protected $end;

	protected $player_positions;

	public function __construct($x_size, $y_size)
	{
		// Initilize some vars [x]
		$wall_list = array();
		$grid;

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
		return $grid;

	}

	protected function get_pos($player)
	{
		return $this->player_positions[$player->get_phone_number()];
	}

	public function join($player)
	{
		$parent->join($player);
		$this->player_positions[$this->player->get_phone_number()] = $this->start;
	}

	public function move($player, $direction, $times)
	{
		$current_postition = get_pos($player);

		if($direction %2 == 1)
			for($i = $current_position[1]; $i < $times; $i += 1* $direction - 2)
			{
				
			}
	}

	public function say($player, $message)
	{

	}

	public function leave($player)
	{

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