<?php

declare(strict_types=1);

namespace App\Command;

use App\Services\ManagedFiles\Service\Interfaces\ManagedFileServiceInterface;
use App\Services\ManagedImages\Services\Interfaces\ManagedImageServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:images:migrate',
    description: 'Migrates managed files to Managed Images.',
)]
class MigrateImagesCommand extends Command
{
    public function __construct(
        private readonly ManagedFileServiceInterface  $managedFileService,
        private readonly ManagedImageServiceInterface $managedImageService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $migratedCount = 0;
        $alreadyMigratedCount = 0;

        $files = $this->managedFileService->findByMimeTypes(mimeTypes: [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/gif',
        ]);

        foreach ($files as $file) {
            $existingManagedImage = $this->managedImageService->findBySourceImage(managedFile: $file);
            if ($existingManagedImage !== null) {
                ++$alreadyMigratedCount;
                continue;
            }

            $this->managedImageService->migrateManagedFile(managedFile: $file);
            ++$migratedCount;
        }

        $io->success(sprintf(
            'Managed image migration complete. Created %d images, skipped %d existing images.',
            $migratedCount,
            $alreadyMigratedCount,
        ));

        return Command::SUCCESS;
    }
}
