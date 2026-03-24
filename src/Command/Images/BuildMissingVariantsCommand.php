<?php

declare(strict_types=1);

namespace App\Command\Images;

use App\Services\ManagedImages\Domain\ImageTypeFormatMap;
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\ManagedImages\Domain\ManagedImageDerivativeFormat;
use App\Services\ManagedImages\Infrastructure\ImageTypeFormatMapRepository;
use App\Services\ManagedImages\Infrastructure\ManagedImageRepository;
use App\Services\ManagedImages\Services\Interfaces\ManagedImageServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
        name: 'app:images:build-missing-variants',
    description: 'Builds any missing managed image variants.',
)]
class BuildMissingVariantsCommand extends Command
{
    public function __construct(
        private readonly ManagedImageRepository $managedImageRepository,
        private readonly ImageTypeFormatMapRepository $imageTypeFormatMapRepository,
        private readonly ManagedImageServiceInterface $managedImageService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $processedImageCount = 0;
        $skippedImageCount = 0;
        $generatedVariantCount = 0;

        foreach ($this->managedImageRepository->findAll() as $managedImage) {
            if (!$managedImage instanceof ManagedImage) {
                continue;
            }

            $imageTypeFormatMap = $this->imageTypeFormatMapRepository->findOneByManagedImageType($managedImage->getType());
            if (!$imageTypeFormatMap instanceof ImageTypeFormatMap) {
                ++$skippedImageCount;

                continue;
            }

            $missingFormats = $this->findMissingFormats($managedImage, $imageTypeFormatMap);
            if ($missingFormats === []) {
                ++$skippedImageCount;

                continue;
            }

            $this->managedImageService->generateMissingVariants($managedImage, $missingFormats);
            ++$processedImageCount;
            $generatedVariantCount += count($missingFormats);
        }

        $io->success(sprintf(
            'Managed image variant build complete. Updated %d images, generated %d variants, skipped %d images.',
            $processedImageCount,
            $generatedVariantCount,
            $skippedImageCount,
        ));

        return Command::SUCCESS;
    }

    /**
     * @return list<ManagedImageDerivativeFormat>
     */
    private function findMissingFormats(ManagedImage $managedImage, ImageTypeFormatMap $imageTypeFormatMap): array
    {
        $missingFormats = [];
        foreach ($imageTypeFormatMap->getFormats() as $requiredFormat) {
            if (!$requiredFormat instanceof ManagedImageDerivativeFormat) {
                continue;
            }

            if ($managedImage->getDerivative($requiredFormat->getHandle()) !== null) {
                continue;
            }

            $missingFormats[] = $requiredFormat;
        }

        return $missingFormats;
    }
}
