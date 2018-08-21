(function ($) {
    var maze, player;

    function $c(cell) {
        if (cell instanceof Cell) {
            return $('#' + cell.id);
        }

        return $('#' + cell);
    }

    function startGame(callback) {
        $('#wrapper').removeClass('playing').empty();
        maze = new Maze($('#width').val(), $('#height').val());

        // Generate cells markup.
        var cell, tmp, cells = '';
        for (var c in maze.cells) {
            cell = maze.cells[c];

            tmp = $('<div>')
                    .attr('id', cell.id)
                    .addClass('cell')
                    .toggleClass('bu', cell.up === false)
                    .toggleClass('br', cell.right === false)
                    .toggleClass('bd', cell.down === false)
                    .toggleClass('bl', cell.left === false);

            cells += $('<div>').append(tmp).html();
        }

        // Draw the maze.
        $('#wrapper').addClass('border').addClass('playing');
        $('<div>').html(cells).attr('id', 'maze').css({width: maze.width * 30, height: maze.height * 30}).appendTo('#wrapper');

        // Customize special points.
        $c(maze.start).addClass('player').addClass('visited');
        $c(maze.end).addClass('exit');

        // Initialize player.
        player = maze.cells[maze.start];
        enlightPlayer();

        // Callback.
        if (typeof callback === 'function') {
            callback();
        }
    }

    function endGame() {
        $('#wrapper').removeClass('playing');
        $('.cell').addClass('visible');
        $('#' + maze.solution.join(',#')).addClass('path');
    }

    function enlightPlayer() {
        var cells = [];
        for (var y = player.y - 2; y < player.y + 3; y++) {
            for (var x = player.x - 2; x < player.x + 3; x++) {
                cells.push('#' + y + '-' + x);
            }
        }
        $(cells.join(',')).addClass('visible');
    }

    function movePlayer(direction) {
        if (!player[direction]) {
            return;
        }

        $c(player).removeClass('player');
        player = player[direction];
        $c(player).addClass('player').addClass('visited');
        enlightPlayer();

        if (player.id == maze.end) {
            endGame();
            alert('Congratulations, you win!');
        }
    }

    $('document').ready(function () {
        // Manage arrow keys.
        $(document).keyup(function (e) {
            if (!$('#wrapper').hasClass('playing')) {
                return;
            }

            var direction;
            switch (e.which) {
                case 37:
                    direction = 'left';
                    break;
                case 38:
                    direction = 'up';
                    break;
                case 39:
                    direction = 'right';
                    break;
                case 40:
                    direction = 'down';
                    break;
                default:
                    return;
            }

            e.preventDefault();
            movePlayer(direction);
        });

        // New game button.
        $('#new-game').click(function () {
            if (!$('#wrapper').hasClass('playing') || confirm('Do you really want to create a new game?')) {
                startGame();
            }
        });

        // Abandon button.
        $('#abandon').click(function () {
            if ($('#wrapper').hasClass('playing') && confirm('Do you really want to abandon this game?')) {
                endGame();
            }
        });

        // Launch the game.
        startGame(function () {
            alert('Welcome to "Escape The Maze"!\n\nUse your arrows keys to move and find the maze exit.')
        });
    });
}(jQuery));