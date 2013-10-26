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

	public function join($new_player)
	{
		if(!$this->has_player())
			$this->players[] = $new_player;
	}

}

class MazeModel extends GameModel
{
	protected $grid;
	protected $start;
	protected $end;

	public function generate_maze($x_size, $y_size)
	{
		$wall_list = new Array();
		$grid = new Array();

		for(i = 0; i < $x_size; $i++)
			for(j = 0; j < $y_size; $j++)
				$grid[i][j] = "#";

		$side = rand(1,4);

		if($side == 1)
			$this->start = (rand(0, $x_size-1), 0);
		else if($side == 2)
			$this->start = ($x_size-1, rand(0, $y_size-1));
		else if($side == 3)
			$this->start = (rand(0, $x_size-1), $y_size-1);
		else if($side == 4)
			$this->start = (0, rand(0, $y_size-1));

		$current = $this->start;
		$grid[$current[0]][$current[1]] = " ";

		if($current[0]+1 < $x_size)
			$wall_list[] = array($current[0]+1, $current[1]);

		if($current[0]-1 > 0)
			$wall_list[] = array($current[0]-1, $current[1]);

		if($current[1]+1 < $y_size)
			$wall_list[] = array($current[0], $current[1]+1);

		if($current[1]-1 > 0)
			$wall_list[] = array($current[0], $current[1]-1);

		while(sizeof($wall_list) > 0)
		{
			$current_wall = rand(0, sizeof($wall_list));

			$adjacent_spaces = 0;
			$potential_spaces = Array();

			if($wall_list[$current_wall][0]+1] < $x_size)
				if($grid[$wall_list[$current_wall][0]+1][$wall_list[$current_wall][1]] == " ")
					$adjacent_spaces += 1;
				else
					$potential_space[] = array($wall_list[$current_wall][0]+1, $wall_list[$current_wall][1]);

			if($wall_list[$current_wall][0]-1] > 0)
				if($grid[$wall_list[$current_wall][0]-1][$wall_list[$current_wall][1]] == " ")
					$adjacent_spaces += 1;
				else
					$potential_spaces[] = array($wall_list[$current_wall][0]-1, $wall_list[$current_wall][1]);

			if($wall_list[$current_wall][1]+1] < $y_size)
				if($grid[$wall_list[$current_wall][0]][$wall_list[$current_wall][1]+1] == " ")
					$adjacent_spaces += 1;
				else
					$potential_spaces[] = array($wall_list[$current_wall][0], $wall_list[$current_wall][1]+1);

			if($wall_list[$current_wall][1]-1] > 0)
				if($grid[$wall_list[$current_wall][0]][$wall_list[$current_wall][1]-1] == " ")
					$adjacent_spaces += 1;
				else
					$potential_spaces[] = array($wall_list[$current_wall][0], $wall_list[$current_wall][1]-1);

			if($adjacent_spaces == 1)
			{
				$new_space = rand(0, sizeof($potential_spaces));
				$grid[$potential_spaces[$new_space][0]][$potential_spaces[$new_space][1]] = " ";

				for($i = 0, $i < sizeof($potential_spaces); $i++)
					if($i != $new_space)
						$wall_list[] = $potential_spaces[$i];
			}

			if(sizeof($wall_list) == 1)
				$this->end_point = $wall_list[$current_wall];

			unset($wall_list[$current_wall]);
		}

		$this->grid = $grid;
		return $grid;

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
		for($i = 0; $i < sizeof($this->grid)); $i++)
		{
			for($j = 0; $j < sizeof($this->grid[$i]; $j++))
				echo $this->grid[$i][$j];
			echo '\n';
		}
	}
}