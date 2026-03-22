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
    name: 'app:files:deduplicate',
    description: 'Deduplicates files.'
)]
class DeduplicateFilesCommand extends Command
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
        $deduplicatedFilesCount = $this->managedFileService->deduplicateFiles();
        $io->success(sprintf('Deduplicated %d ManagedFile records.', $deduplicatedFilesCount));
        return Command::SUCCESS;
    }
}
