<?php

declare(strict_types=1);

use App\Message\ManagedImageVariantsUpdateMessage;
use App\MessageHandler\ManagedImageVariantsUpdateMessageHandler;
use App\Services\ManagedFiles\Domain\ManagedFile;
use App\Services\ManagedImages\Domain\Enums\ManagedImageTypeEnum;
use App\Services\ManagedImages\Domain\ImageTypeFormatMap;
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\ManagedImages\Domain\ManagedImageDerivative;
use App\Services\ManagedImages\Domain\ManagedImageDerivativeFormat;
use App\Services\ManagedImages\Infrastructure\ImageTypeFormatMapRepository;
use App\Services\ManagedImages\Infrastructure\ManagedImageRepository;
use App\Services\ManagedImages\Services\Interfaces\ManagedImageServiceInterface;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;

beforeEach(function () {
    $this->managedImageRepository = $this->createMock(ManagedImageRepository::class);
    $this->imageTypeFormatMapRepository = $this->createMock(ImageTypeFormatMapRepository::class);
    $this->managedImageService = $this->createMock(ManagedImageServiceInterface::class);

    $this->makeManagedFile = function (array $overrides = []): ManagedFile {
        return new ManagedFile(
            path: $overrides['path'] ?? '/uploads/test-image.webp',
            fileHash: $overrides['fileHash'] ?? '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef',
            mimeType: $overrides['mimeType'] ?? 'image/webp',
            title: $overrides['title'] ?? 'Test image',
            description: $overrides['description'] ?? 'Test description',
            uploader: $overrides['uploader'] ?? $this->createStub(User::class),
            id: $overrides['id'] ?? null,
        );
    };

    $this->makeFormat = function (array $overrides = []): ManagedImageDerivativeFormat {
        return new ManagedImageDerivativeFormat(
            handle: $overrides['handle'] ?? 'thumbnail',
            resolutionX: $overrides['resolutionX'] ?? 256,
            quality: $overrides['quality'] ?? 85,
            resolutionY: $overrides['resolutionY'] ?? null,
            allowCrop: $overrides['allowCrop'] ?? false,
            centreImage: $overrides['centreImage'] ?? false,
            id: $overrides['id'] ?? null,
        );
    };

    $this->makeManagedImage = function (array $overrides = []): ManagedImage {
        return new ManagedImage(
            description: $overrides['description'] ?? 'Test managed image',
            createdBy: $overrides['createdBy'] ?? $this->createStub(User::class),
            sourceImage: $overrides['sourceImage'] ?? ($this->makeManagedFile)(),
            type: $overrides['type'] ?? ManagedImageTypeEnum::DEFAULT,
            derivatives: $overrides['derivatives'] ?? new ArrayCollection(),
            id: $overrides['id'] ?? 42,
        );
    };
});

test('handler returns when managed image cannot be found', function () {
    $this->managedImageRepository
        ->expects($this->once())
        ->method('find')
        ->with(42)
        ->willReturn(null);
    $this->imageTypeFormatMapRepository->expects($this->never())->method('findOneByManagedImageType');
    $this->managedImageService->expects($this->never())->method('generateMissingVariants');

    $handler = new ManagedImageVariantsUpdateMessageHandler(
        managedImageRepository: $this->managedImageRepository,
        imageTypeFormatMapRepository: $this->imageTypeFormatMapRepository,
        managedImageService: $this->managedImageService,
    );

    $handler(new ManagedImageVariantsUpdateMessage(42));

    self::assertTrue(true);
});

test('handler skips generation when all required formats already exist', function () {
    $thumbnailFormat = ($this->makeFormat)(['handle' => 'thumbnail']);
    $thumbnailDerivative = new ManagedImageDerivative(
        parentImage: $this->createStub(ManagedImage::class),
        format: $thumbnailFormat,
        derivative: ($this->makeManagedFile)(['path' => '/uploads/thumbnail.webp']),
    );
    $managedImage = ($this->makeManagedImage)([
        'type' => ManagedImageTypeEnum::THUMBNAIL,
        'derivatives' => new ArrayCollection([$thumbnailDerivative]),
    ]);
    $map = new ImageTypeFormatMap(
        managedImageType: ManagedImageTypeEnum::THUMBNAIL,
        formats: new ArrayCollection([$thumbnailFormat]),
        id: 1,
    );

    $this->managedImageRepository->method('find')->with(42)->willReturn($managedImage);
    $this->imageTypeFormatMapRepository->method('findOneByManagedImageType')->with(ManagedImageTypeEnum::THUMBNAIL)->willReturn($map);

    $this->managedImageService->expects($this->never())->method('generateMissingVariants');

    $handler = new ManagedImageVariantsUpdateMessageHandler(
        managedImageRepository: $this->managedImageRepository,
        imageTypeFormatMapRepository: $this->imageTypeFormatMapRepository,
        managedImageService: $this->managedImageService,
    );

    $handler(new ManagedImageVariantsUpdateMessage(42));
});

test('handler delegates missing required formats to managed image service', function () {
    $thumbnailFormat = ($this->makeFormat)(['handle' => 'thumbnail']);
    $avatarThumbnailFormat = ($this->makeFormat)(['handle' => 'avatar-thumbnail']);
    $thumbnailDerivative = new ManagedImageDerivative(
        parentImage: $this->createStub(ManagedImage::class),
        format: $thumbnailFormat,
        derivative: ($this->makeManagedFile)(['path' => '/uploads/thumbnail.webp']),
    );
    $managedImage = ($this->makeManagedImage)([
        'type' => ManagedImageTypeEnum::DEFAULT,
        'derivatives' => new ArrayCollection([$thumbnailDerivative]),
    ]);
    $map = new ImageTypeFormatMap(
        managedImageType: ManagedImageTypeEnum::DEFAULT,
        formats: new ArrayCollection([$thumbnailFormat, $avatarThumbnailFormat]),
        id: 1,
    );

    $this->managedImageRepository->method('find')->with(42)->willReturn($managedImage);
    $this->imageTypeFormatMapRepository->method('findOneByManagedImageType')->with(ManagedImageTypeEnum::DEFAULT)->willReturn($map);

    $this->managedImageService->expects($this->once())
        ->method('generateMissingVariants')
        ->with(
            $managedImage,
            $this->callback(static function (array $missingFormats): bool {
                return count($missingFormats) === 1
                    && $missingFormats[0] instanceof ManagedImageDerivativeFormat
                    && $missingFormats[0]->getHandle() === 'avatar-thumbnail';
            }),
        );

    $handler = new ManagedImageVariantsUpdateMessageHandler(
        managedImageRepository: $this->managedImageRepository,
        imageTypeFormatMapRepository: $this->imageTypeFormatMapRepository,
        managedImageService: $this->managedImageService,
    );

    $handler(new ManagedImageVariantsUpdateMessage(42));
});
