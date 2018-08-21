<?php
// Enable error reporting. 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include required classes.
require_once './../php/Cell.php';
require_once './../php/Maze.php';

// Generate the maze.
$maze = new Maze(40, 40);

// Select two random points.
do {
    $p1 = $maze->randomCell();
} while ($p1->id === $maze->start() || $p1->id === $maze->end());

do {
    $p2 = $maze->randomCell();
} while ($p2->id === $maze->start() || $p2->id === $maze->end() || $p2->id === $p1->id);

// Prepare pathes to display.
$p1p2 = '#' . implode(',#', $maze->path($p1, $p2));
$solution = '#' . implode(',#', $maze->solution());

// Prepare cells markup.
$cells = '';
$template = '<div id="%s" data-x="%d" data-y="%d" class="%s" data-path="%s" title="%s"></div>';
foreach ($maze->cells() as $cell) {
    // Get cell attributes.
    $title = '';
    $path = '';
    $class = array_keys(array_filter([
        'cell' => true,
        'bu' => ($cell->up === false),
        'br' => ($cell->right === false),
        'bd' => ($cell->down === false),
        'bl' => ($cell->left === false)
    ]));

    // Customize special points.
    if ($cell->id === $maze->start()) {
        $title = 'Maze entrance';
        $class[] = 'entrance';
        $path = $solution;
    }
    if ($cell->id === $maze->end()) {
        $title = 'Maze exit';
        $class[] = 'exit';
        $path = $solution;
    }
    if ($cell->id === $p1->id || $cell->id === $p2->id) {
        $path = $p1p2;
        $title = 'Random point';
        $class[] = 'random';
    }

    // Populate template.
    $cells .= sprintf($template, $cell->id, $cell->x, $cell->y, implode(' ', $class), $path, $title);
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Maze PHP</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="demo.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div id="wrapper" class="border">
            <div id="maze" style="width:<?= $maze->width() * 20 ?>px; height:<?= $maze->height() * 20 ?>px;">
                <?= $cells ?>
            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script>
            (function ($) {
                $('document').ready(function () {
                    // Show path on special points hover.
                    $('[data-path!=""]').hover(function () {
                        $($(this).data('path')).addClass('path');
                    }, function () {
                        $($(this).data('path')).removeClass('path');
                    });
                });
            }(jQuery));
        </script>
    </body>
</html>