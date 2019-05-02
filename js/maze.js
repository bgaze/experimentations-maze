function Maze(width, height) {
    // Private.

    var opposites = {up: 'down', right: 'left', down: 'up', left: 'right'};
    var cell, unexplored, next, path, direction;

    function rand(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    function generate() {
        // Create maze cells and build a list to store links to keep.
        var links = {};
        for (var y = 0; y < this.height; y++) {
            for (var x = 0; x < this.width; x++) {
                this.cells[y + '-' + x] = new Cell(this, x, y);
                links[y + '-' + x] = {up: false, right: false, down: false, left: false};
            }
        }

        // Select randomly maze exit.
        cell = this.randomCell();
        this.end = cell.id;

        // Dig the maze.
        var len = 0;
        path = [];
        do {
            // Mark cell as visited.
            cell.visited = true;

            // If all of neighbours have already been explored, back up the path.
            unexplored = Object.keys(cell.unexplored);
            if (!unexplored.length) {
                cell = this.cells[path.pop()];
                continue;
            }

            // Get next cell.
            direction = unexplored[Math.floor(Math.random() * unexplored.length)];
            next = this.cells[cell.unexplored[direction]];
            delete cell.unexplored[direction];

            // If it was already visited, skip it.
            if (next.visited) {
                continue;
            }

            // Add current cell to path.
            path.push(cell.id);

            // If longest path, save as maze solution.
            if (path.length > len) {
                len = path.length;
                this.start = cell.id;
                this.solution = path.slice();
            }

            // Remeber link.
            links[cell.id][direction] = next.id;
            links[next.id][opposites[direction]] = cell.id;

            // Move to next cell.
            cell = next;
        } while (path.length > 0);

        // Sync valid links.
        for (var id in links) {
            for (direction in links[id]) {
                this.cells[id][direction] = links[id][direction] ? this.cells[links[id][direction]] : false;
            }
        }

        // Remove start & end from solution.
        this.solution.shift();
        this.solution.pop();
        
        // Reverse solution.
        this.solution = this.solution.reverse();
    }

    // Public.

    this.cells = {};
    this.width = width;
    this.height = height;
    this.solution = [];

    this.randomCell = function () {
        return this.cells[rand(0, this.height - 1) + '-' + rand(0, this.width - 1)];
    };

    this.path = function (from, to) {
        // Get path begin.
        if (!(from instanceof Cell)) {
            from = this.cells[from];
        }

        // Get path destination.
        if (typeof to === 'undefined') {
            to = this.end;
        }
        if (!(to instanceof Cell)) {
            to = this.cells[to];
        }

        // Reset cells statuses.
        for (var c in this.cells) {
            this.cells[c].reset();
        }

        // Walk the maze to find path.
        path = [];
        cell = from;
        do {
            unexplored = Object.keys(cell.unexplored);

            // If all of neighbours have already been explored, back up the path.
            if (!unexplored.length) {
                cell = this.cells[path.pop()];
                continue;
            }

            // Add current cell to path.
            path.push(cell.id);

            // Get next cell.
            direction = unexplored[Math.floor(Math.random() * unexplored.length)];
            next = cell.unexplored[direction];

            // Mark cells as explored.
            if (typeof cell.unexplored[direction] !== 'undefined') {
                delete cell.unexplored[direction];
            }
            if (typeof next.unexplored[opposites[direction]] !== 'undefined') {
                delete next.unexplored[opposites[direction]];
            }

            // Move to next cell.
            cell = next;
        } while (cell.id != to.id);

        // Remove start point from path.
        path.shift();

        // Return the path.
        return path;
    };

    // Construct.

    generate.call(this);
}