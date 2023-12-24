<?php

declare(strict_types=1);

namespace WickedByte\App\Commands;

use Random\Randomizer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use WickedByte\App\BoundedStepper;
use WickedByte\App\ConsoleRenderer;
use WickedByte\App\Domain\Cell;
use WickedByte\App\Domain\Grid;

class GameOfLifeCommand extends Command
{
    /**
     * @var array<string,int>
     */
    final public const array SPEEDS = [
        'slow' => 1000000,
        'normal' => 500000,
        'fast' => 250000,
        'extra-fast' => 50000,
    ];

    #[\Override]
    protected function configure(): void
    {
        $this->setName('game-of-life');
        $this->setDescription("Play Conway's Game of Life");
        $this->addOption('grid', 'g', InputOption::VALUE_REQUIRED, <<<'EOF'
            Use a template grid (glider, blinker, acorn, etc)
            EOF);
        $this->addOption('ticks', 't', InputOption::VALUE_REQUIRED, <<<'EOF'
            Automatically run for a number of ticks
            EOF);
        $this->addOption('weight', 'w', InputOption::VALUE_REQUIRED, <<<'EOF'
            Percentage of cells to be alive at start (0-100) for random grid (default: 80)
            EOF);
        $this->addOption('speed', 's', InputOption::VALUE_REQUIRED, <<<'EOF'
            Speed of ticks in microseconds ("slow", "normal", "fast", "extra-fast")
            EOF);
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $max_ticks = (int)($this->getScalarOption($input, 'ticks') ?? 0);
        $template = (string)$this->getScalarOption($input, 'grid');
        $weight = (int)($this->getScalarOption($input, 'weight') ?? 80);
        $delay = self::SPEEDS[$this->getScalarOption($input, 'speed')] ?? self::SPEEDS['normal'];

        $helper = $this->getHelper('question');
        \assert($helper instanceof QuestionHelper);
        $question = new ConfirmationQuestion('next? (q to exit)', true);

        $stepper = new BoundedStepper();
        $renderer = new ConsoleRenderer();
        $grid = $template !== ''
            ? $this->initializeTemplateGrid($template,)
            : $this->initializeRandomGrid($renderer, $weight);

        \assert($output instanceof ConsoleOutputInterface);
        $section = $output->section();
        $section->writeln('');

        $ticks = 0;
        do {
            $section->overwrite('');
            $section->writeln($renderer->render($grid));
            $section->writeln('tick count: ' . $ticks);

            $live_count = \count($grid->cells(true));
            $section->writeln(\vsprintf("live cells: %s / %s (%s%%)", [
                $live_count,
                $renderer->size(),
                \round($live_count / $renderer->size() * 100, 2),
            ]));

            $grid = $stepper->tick($grid);

            ++$ticks;
            if ($max_ticks !== 0) {
                \usleep($delay);
            }
        } while ($max_ticks !== 0 ? $ticks <= $max_ticks : $helper->ask($input, $output, $question));

        return self::SUCCESS;
    }

    private function initializeTemplateGrid(string $template, Grid $grid = new Grid()): Grid
    {
        $template = (string)\file_get_contents(__DIR__ . '/../../resources/templates/' . $template . '.txt');
        $template !== '' || throw new \RuntimeException('Invalid template: ' . $template);

        $lines = \explode("\n", \trim($template));
        $y = \count($lines) - 1;

        foreach ($lines as $line) {
            $x = 0;
            foreach (\str_split($line) as $char) {
                if ($char === 'L') {
                    $grid->add(Cell::live($x, $y));
                }

                ++$x;
            }

            --$y;
        }

        return $grid;
    }

    private function initializeRandomGrid(
        ConsoleRenderer $renderer,
        int|float $weight = 80,
        Grid $grid = new Grid(),
        Randomizer $randomizer = new Randomizer(),
    ): Grid {
        for ($x = $renderer->min_x; $x <= $renderer->max_x; ++$x) {
            for ($y = $renderer->min_y; $y <= $renderer->max_y; ++$y) {
                if ($randomizer->getFloat(0, 100) < $weight) {
                    $grid->add(Cell::live($x, $y));
                }
            }
        }

        return $grid;
    }

    private function getScalarOption(InputInterface $input, string $name): string|bool|int|float|null
    {
        $value = $input->getOption($name);
        if ($value === null || \is_scalar($value)) {
            return $value;
        }

        throw new \InvalidArgumentException(\sprintf('The "%s" option must be scalar.', $name));
    }
}
