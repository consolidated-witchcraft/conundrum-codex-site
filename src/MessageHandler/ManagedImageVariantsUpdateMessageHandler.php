<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\ManagedImageVariantsUpdateMessage;
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\ManagedImages\Domain\ManagedImageDerivativeFormat;
use App\Services\ManagedImages\Domain\ImageTypeFormatMap;
use App\Services\ManagedImages\Infrastructure\ImageTypeFormatMapRepository;
use App\Services\ManagedImages\Infrastructure\ManagedImageRepository;
use App\Services\ManagedImages\Services\Interfaces\ManagedImageServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ManagedImageVariantsUpdateMessageHandler
{
    public function __construct(
        private readonly ManagedImageRepository $managedImageRepository,
        private readonly ImageTypeFormatMapRepository $imageTypeFormatMapRepository,
        private readonly ManagedImageServiceInterface $managedImageService,
    ) {
    }

    public function __invoke(ManagedImageVariantsUpdateMessage $message): void
    {
        $managedImage = $this->managedImageRepository->find($message->getManagedImageId());
        if (!$managedImage instanceof ManagedImage) {
            return;
        }

        $imageTypeFormatMap = $this->imageTypeFormatMapRepository->findOneByManagedImageType($managedImage->getType());
        if (!$imageTypeFormatMap instanceof ImageTypeFormatMap) {
            return;
        }

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

        if ($missingFormats === []) {
            return;
        }

        $this->managedImageService->generateMissingVariants($managedImage, $missingFormats);
    }
}
