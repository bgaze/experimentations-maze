function Cell(maze, x, y) {
    var self = this;

    this.x = x;
    this.y = y;
    this.id = y + '-' + x;
    this.visited = false;
    this.unexplored = {};

    this.up = (y - 1 >= 0) ? (y - 1) + '-' + x : false;
    this.right = (x + 1 < maze.width) ? y + '-' + (x + 1) : false;
    this.down = (y + 1 < maze.height) ? (y + 1) + '-' + x : false;
    this.left = (x - 1 >= 0) ? y + '-' + (x - 1) : false;

    this.reset = function () {
        self.visited = false;
        self.unexplored = {};

        if (self.up) {
            self.unexplored['up'] = self.up;
        }

        if (self.right) {
            self.unexplored['right'] = self.right;
        }

        if (self.down) {
            self.unexplored['down'] = self.down;
        }

        if (self.left) {
            self.unexplored['left'] = self.left;
        }
    };

    this.reset();
}