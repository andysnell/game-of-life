# Game of Life

## Installation
Run `make` to build the project Docker image, create the "./build" cache directory
and install vendor dependencies with Composer.

To upgrade the project dependencies to their current major versions, run `make upgrade`, or
to just update them within the bounds of their currently defined constraints, run `make update`

Common Actions with Makefile targets:
 - `make bash`
 - `make phpunit`
 - `make phpbench`
 - `make psysh`
 - `make phpcs`
 - `make phpstan`
 - `make rector`
 - `make ci`

To get a fresh start, run `make clean` to delete the vendor and build directories,
which will trigger a docker image rebuild, the next time `make` or `make build` is run.

For anything else not defined in the Makefile, use:
```shell
docker compose run --rm -it app {your-command-here}
```

## Rules

1. Any live cell with fewer than two live neighbors dies, as if by underpopulation.
2. Any live cell with two or three live neighbors lives on to the next generation.
3. Any live cell with more than three live neighbors dies, as if by overpopulation.
4. Any dead cell with exactly three live neighbors becomes a live cell, as if by reproduction.


## Usage

With Preset Template Grid:
```shell
docker compose run --rm -it app php app game-of-life --preset=glider
```

With Random Grid:
```shell
docker compose run --rm -it app php app game-of-life
```

With Automatic Ticks:
```shell
docker compose run --rm -it app php app game-of-life --ticks=25 --speed=normal
```
