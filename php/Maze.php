<?php

class Maze {

    protected $height;
    protected $width;
    protected $start;
    protected $end;
    protected $cells = [];
    protected $solution = [];

    function __construct($width, $height) {
        $this->cells = [];
        $this->height = $height;
        $this->width = $width;

        // Generate maze.
        $this->generate();
    }

    protected function generate() {
        // Create maze cells and build a list to store links to keep.
        $links = [];
        for ($y = 0; $y < $this->height; $y++) {
            for ($x = 0; $x < $this->width; $x++) {
                $this->cells[$y . '-' . $x] = new Cell($this, $x, $y);
                $links[$y . '-' . $x] = ['up' => false, 'right' => false, 'down' => false, 'left' => false];
            }
        }

        // Select randomly maze exit.
        $cell = $this->randomCell();
        $this->end = $cell->id;

        // Dig the maze.
        $opposites = ['up' => 'down', 'right' => 'left', 'down' => 'up', 'left' => 'right'];
        $path = [];
        $length = 0;
        do {
            // Mark cell as visited.
            $cell->visited = true;

            // If all of neighbours have already been explored, back up the path.
            if (empty($cell->unexplored)) {
                $cell = array_pop($path);
                continue;
            }

            // Get next cell.
            $direction = array_rand($cell->unexplored);
            $next = $this->cells[$cell->unexplored[$direction]];
            unset($cell->unexplored[$direction]);

            // If it was already visited, skip it.
            if ($next->visited) {
                continue;
            }

            // Add current cell to path.
            $path[$cell->id] = $cell;

            // If longest path, save as maze solution.
            if (count($path) > $length) {
                $length = count($path);
                $this->start = $cell->id;
                $this->solution = array_keys($path);
            }

            // Remeber link.
            $next->visited = true;
            $links[$cell->id][$direction] = $next->id;
            $links[$next->id][$opposites[$direction]] = $cell->id;

            // Move to next cell.
            $cell = $next;
        } while (!empty($path));

        // Sync valid links.
        foreach ($links as $id => $directions) {
            foreach ($directions as $direction => $target) {
                $this->cells[$id]->{$direction} = $target ? $this->cells[$target] : false;
            }
        }

        // Remove start & end from solution.
        array_shift($this->solution);
        array_pop($this->solution);
    }

    public function randomCell() {
        return $this->cells[array_rand($this->cells)];
    }

    public function path($from, $to = null) {
        // Get path begin.
        if (!($from instanceof Cell)) {
            $from = $this->cells[$from];
        }

        // Get path destination.
        if (empty($to)) {
            $to = $this->end;
        }
        if (!($to instanceof Cell)) {
            $to = $this->cells[$to];
        }

        // Reset cells statuses.
        foreach ($this->cells as $cell) {
            $cell->reset();
        }

        // Walk the maze to find path.
        $opposites = ['up' => 'down', 'right' => 'left', 'down' => 'up', 'left' => 'right'];
        $path = [];
        $cell = $from;
        do {
            // If all of neighbours have already been explored, back up the path.
            if (empty($cell->unexplored)) {
                $cell = array_pop($path);
                continue;
            }

            // Add current cell to path.
            $path[$cell->id] = $cell;

            // Get next cell.
            $direction = array_rand($cell->unexplored);
            $next = $cell->unexplored[$direction];

            // Mark cells as explored.
            if (isset($cell->unexplored[$direction])) {
                unset($cell->unexplored[$direction]);
            }
            if (isset($next->unexplored[$opposites[$direction]])) {
                unset($next->unexplored[$opposites[$direction]]);
            }

            // Move to next cell.
            $cell = $next;
        } while ($cell->id !== $to->id);

        // Remove start point from path.
        array_shift($path);

        // Return the path.
        return array_keys($path);
    }

    public function cells() {
        return $this->cells;
    }

    public function height() {
        return $this->height;
    }

    public function width() {
        return $this->width;
    }

    public function start() {
        return $this->start;
    }

    public function end() {
        return $this->end;
    }

    public function solution() {
        return $this->solution;
    }

}
