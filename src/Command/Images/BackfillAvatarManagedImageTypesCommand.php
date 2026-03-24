<?php

declare(strict_types=1);

namespace App\Command\Images;

use App\Services\ManagedImages\Domain\Enums\ManagedImageTypeEnum;
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\ManagedImages\Infrastructure\ManagedImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:images:backfill-avatar-types',
    description: 'Backfills managed images with avatar descriptions to avatar type.',
)]
class BackfillAvatarManagedImageTypesCommand extends Command
{
    private const string AVATAR_DESCRIPTION_SUFFIX = ' avatar';

    public function __construct(
        private readonly ManagedImageRepository $managedImageRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $updatedImagesCount = 0;

        foreach (
            $this->managedImageRepository->findAllByDescriptionSuffixAndType(
                self::AVATAR_DESCRIPTION_SUFFIX,
                ManagedImageTypeEnum::DEFAULT,
            ) as $managedImage
        ) {
            if (!$managedImage instanceof ManagedImage) {
                continue;
            }

            $managedImage->setType(ManagedImageTypeEnum::AVATAR);
            ++$updatedImagesCount;
        }

        $this->entityManager->flush();

        $io->success(sprintf(
            'Managed image avatar type backfill complete. Updated %d images.',
            $updatedImagesCount,
        ));

        return Command::SUCCESS;
    }
}
