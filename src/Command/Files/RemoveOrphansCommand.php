<?php

declare(strict_types=1);

namespace App\Command\Files;

use App\Services\ManagedFiles\Service\Interfaces\ManagedFileServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:files:remove-orphans',
    description: 'Remove orphaned files.'
)]
class RemoveOrphansCommand extends Command
{

    protected function configure(): void
    {
    }

    public function __construct(
        private readonly ManagedFileServiceInterface  $managedFileService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
        $orphanedFiles = $this->managedFileService->findOrphans();
        if (count($orphanedFiles) === 0) {
            $io->info('No files found. Exiting');
            return Command::SUCCESS;
        }
        foreach ($orphanedFiles as $orphanedFile) {
            $io->info(sprintf('Removing orphaned file: %s', $orphanedFile->getPath()));
            $this->managedFileService->delete($orphanedFile, false);
        }
        $this->managedFileService->flush();
        return Command::SUCCESS;
    }
}
