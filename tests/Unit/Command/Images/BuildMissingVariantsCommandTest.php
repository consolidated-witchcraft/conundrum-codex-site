<?php

declare(strict_types=1);

use App\Command\Images\BuildMissingVariantsCommand;
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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

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

test('command builds only missing variants and reports summary', function () {
    $thumbnailFormat = ($this->makeFormat)(['handle' => 'thumbnail']);
    $avatarThumbnailFormat = ($this->makeFormat)(['handle' => 'avatar-thumbnail']);

    $existingDerivative = new ManagedImageDerivative(
        parentImage: $this->createStub(ManagedImage::class),
        format: $thumbnailFormat,
        derivative: ($this->makeManagedFile)(['path' => '/uploads/thumbnail.webp']),
    );

    $managedImageWithMissingVariant = ($this->makeManagedImage)([
        'type' => ManagedImageTypeEnum::DEFAULT,
        'derivatives' => new ArrayCollection([$existingDerivative]),
        'id' => 10,
    ]);
    $managedImageWithoutMap = ($this->makeManagedImage)([
        'type' => ManagedImageTypeEnum::AVATAR,
        'id' => 11,
    ]);
    $managedImageComplete = ($this->makeManagedImage)([
        'type' => ManagedImageTypeEnum::THUMBNAIL,
        'derivatives' => new ArrayCollection([
            new ManagedImageDerivative(
                parentImage: $this->createStub(ManagedImage::class),
                format: $thumbnailFormat,
                derivative: ($this->makeManagedFile)(['path' => '/uploads/thumbnail-complete.webp']),
            ),
        ]),
        'id' => 12,
    ]);

    $defaultMap = new ImageTypeFormatMap(
        managedImageType: ManagedImageTypeEnum::DEFAULT,
        formats: new ArrayCollection([$thumbnailFormat, $avatarThumbnailFormat]),
        id: 1,
    );
    $thumbnailMap = new ImageTypeFormatMap(
        managedImageType: ManagedImageTypeEnum::THUMBNAIL,
        formats: new ArrayCollection([$thumbnailFormat]),
        id: 2,
    );

    $this->managedImageRepository
        ->expects($this->once())
        ->method('findAll')
        ->willReturn([$managedImageWithMissingVariant, $managedImageWithoutMap, $managedImageComplete]);

    $this->imageTypeFormatMapRepository
        ->expects($this->exactly(3))
        ->method('findOneByManagedImageType')
        ->willReturnMap([
            [ManagedImageTypeEnum::DEFAULT, $defaultMap],
            [ManagedImageTypeEnum::AVATAR, null],
            [ManagedImageTypeEnum::THUMBNAIL, $thumbnailMap],
        ]);

    $this->managedImageService
        ->expects($this->once())
        ->method('generateMissingVariants')
        ->with(
            $managedImageWithMissingVariant,
            $this->callback(static function (array $missingFormats): bool {
                return count($missingFormats) === 1
                    && $missingFormats[0] instanceof ManagedImageDerivativeFormat
                    && $missingFormats[0]->getHandle() === 'avatar-thumbnail';
            }),
        );

    $command = new BuildMissingVariantsCommand(
        managedImageRepository: $this->managedImageRepository,
        imageTypeFormatMapRepository: $this->imageTypeFormatMapRepository,
        managedImageService: $this->managedImageService,
    );

    $commandTester = new CommandTester($command);
    $exitCode = $commandTester->execute([]);
    $display = $commandTester->getDisplay();

    self::assertSame(Command::SUCCESS, $exitCode);
    self::assertStringContainsString('Updated 1 images', $display);
    self::assertStringContainsString('generated 1', $display);
    self::assertStringContainsString('skipped 2 images', $display);
});
