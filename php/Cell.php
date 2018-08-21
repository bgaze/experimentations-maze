<?php

class Cell {

    public $id;
    public $x;
    public $y;
    public $up;
    public $right;
    public $down;
    public $left;
    public $visited;
    public $unexplored;

    function __construct(Maze $maze, $x, $y) {
        $this->id = $y . '-' . $x;

        $this->x = $x;
        $this->y = $y;

        $this->up = ($y - 1) >= 0 ? ($y - 1) . '-' . $x : false;
        $this->right = ($x + 1) < $maze->width() ? $y . '-' . ($x + 1) : false;
        $this->down = ($y + 1) < $maze->height() ? ($y + 1) . '-' . $x : false;
        $this->left = ($x - 1) >= 0 ? $y . '-' . ($x - 1) : false;

        $this->reset();
    }

    public function reset() {
        $this->visited = false;
        $this->unexplored = array_filter([
            'up' => $this->up,
            'right' => $this->right,
            'down' => $this->down,
            'left' => $this->left
        ]);
    }

}
