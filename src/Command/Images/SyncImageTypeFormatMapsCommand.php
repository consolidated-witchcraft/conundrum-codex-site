<?php

declare(strict_types=1);

namespace App\Command\Images;

use App\Services\ManagedImages\Service\ImageTypeFormatMapSynchronizer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:images:sync-format-maps',
    description: 'Synchronises managed image type to derivative format maps.',
)]
class SyncImageTypeFormatMapsCommand extends Command
{
    public function __construct(
        private readonly ImageTypeFormatMapSynchronizer $imageTypeFormatMapSynchronizer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $updatedMapsCount = $this->imageTypeFormatMapSynchronizer->sync();

        $io->success(sprintf(
            'Managed image type format map sync complete. Updated %d maps.',
            $updatedMapsCount,
        ));

        return Command::SUCCESS;
    }
}
