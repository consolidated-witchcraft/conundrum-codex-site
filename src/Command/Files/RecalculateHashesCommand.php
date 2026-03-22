<?php

declare(strict_types=1);

namespace App\Command\Files;

use App\Services\ManagedFiles\Service\Interfaces\ManagedFileServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:files:recalculate-hashes',
    description: 'Recalculate hashes of all files that are missing them.'
)]
class RecalculateHashesCommand extends Command
{
    public function __construct(
        private readonly ManagedFileServiceInterface  $managedFileService,
        #[Autowire('%kernel.project_dir%/public')]
        private readonly string $publicDirectory,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $managedFiles = $this->managedFileService->findAll();

        $status = Command::SUCCESS;
        foreach ($managedFiles as $managedFile) {
            if ($managedFile->getFileHash() !== null) {
                $io->info(sprintf("A hash already exists for managed file %d. Skipping", $managedFile->getId()));
                continue;
            }
            $absolutePath = sprintf("%s%s",$this->publicDirectory, $managedFile->getPath());
            if (!is_file($absolutePath)) {
                $io->warning(sprintf(
                    'ManagedFile #%d: file not found at "%s". Marking as orphaned',
                    $managedFile->getId() ?? 0,
                    $absolutePath,
                ));
                $managedFile->markOrphaned();
                $status = Command::FAILURE;
                continue;
            }

            if (!is_readable($absolutePath)) {
                $io->warning(sprintf(
                    'ManagedFile #%d: file not readable at "%s".',
                    $managedFile->getId() ?? 0,
                    $absolutePath,
                ));
                $status = Command::FAILURE;

            }

            $hash = $this->managedFileService->generateFileHash($absolutePath);
            if ($hash === false) {
                $io->warning(sprintf(
                    'Skipping ManagedFile #%d: hash generation failed for "%s".',
                    $managedFile->getId() ?? 0,
                    $absolutePath,
                ));
                $status = Command::FAILURE;
                continue;
            }
            $managedFile->setFileHash($hash);
            $io->text(sprintf(
                'Updated ManagedFile #%d with SHA-256 hash.',
                $managedFile->getId() ?? 0,
            ));
            $this->managedFileService->save($managedFile, false);
        }

        $this->managedFileService->flush();

        return $status;
    }
}
