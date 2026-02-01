<?php

namespace App\Command;

use App\Services\PuzzleTemplate\Service\Factory\Exceptions\PuzzleTemplateRegistryBuildException;
use App\Services\PuzzleTemplate\Service\Factory\PuzzleTemplateRegistryFactory;
use App\Services\PuzzleTemplate\Service\PuzzleTemplateService;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app.puzzle.templates.rebuild',
    description: 'Rebuild the puzzles Template Cache',
)]
class RebuildpuzzleTemplateCacheCommand extends Command
{
    public function __construct(
        private readonly PuzzleTemplateRegistryFactory $factory,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);


        $io->info('Rebuilding Puzzle Template Cache');
        try {
            $this->factory->generatePuzzleTemplateRegistry(forceRefresh: true, verbose: true);
        } catch (PuzzleTemplateRegistryBuildException $e){
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        $io->success("Puzzle Template Cache rebuilt successfully.");

        return Command::SUCCESS;
    }
}
