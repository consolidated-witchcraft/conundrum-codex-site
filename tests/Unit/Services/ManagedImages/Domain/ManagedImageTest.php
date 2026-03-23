<?php

declare(strict_types=1);

use App\Services\ManagedFiles\Domain\ManagedFile;
use App\Services\ManagedImages\Domain\Exceptions\DuplicateManagedImageDerivativeFormatException;
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\ManagedImages\Domain\ManagedImageDerivative;
use App\Services\ManagedImages\Domain\ManagedImageDerivativeFormat;
use App\Services\ManagedImages\Domain\Enums\ManagedImageTypeEnum;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;

beforeEach(function () {
    $this->makeManagedFile = function (array $overrides = []): ManagedFile {
        return new ManagedFile(
            path: $overrides['path'] ?? 'test/path',
            fileHash: $overrides['fileHash'] ?? '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef',
            mimeType: $overrides['mimeType'] ?? 'image/webp',
            title: $overrides['title'] ?? 'Test Image',
            description: $overrides['description'] ?? 'Test Description',
            uploader: $overrides['uploader'] ?? $this->createStub(User::class),
        );
    };

    $this->makeManagedImage = function (array $overrides = []): ManagedImage {
        return new ManagedImage(
            description: $overrides['description'] ?? 'Test managed image',
            createdBy: $overrides['createdBy'] ?? $this->createStub(User::class),
            sourceImage: $overrides['sourceImage'] ?? ($this->makeManagedFile)(),
            type: $overrides['type'] ?? ManagedImageTypeEnum::DEFAULT,
            derivatives: $overrides['derivatives'] ?? new ArrayCollection(),
            id: $overrides['id'] ?? null,
        );
    };

    $this->makeManagedImageDerivativeFormat = function (array $overrides = []): ManagedImageDerivativeFormat {
        return new ManagedImageDerivativeFormat(
            handle: $overrides['handle'] ?? 'thumbnail',
            resolutionX: $overrides['resolutionX'] ?? 320,
            resolutionY: $overrides['resolutionY'] ?? 180,
            quality: $overrides['quality'] ?? 85,
            id: $overrides['id'] ?? null,
        );
    };
});

test('construct', function () {
    $createdBy = $this->createStub(User::class);
    $sourceImage = ($this->makeManagedFile)(['path' => 'test/source.webp']);
    $derivatives = new ArrayCollection();
    $managedImage = new ManagedImage(
        description: 'Test managed image',
        createdBy: $createdBy,
        sourceImage: $sourceImage,
        type: ManagedImageTypeEnum::THUMBNAIL,
        derivatives: $derivatives,
        id: 12,
    );

    self::assertSame('Test managed image', $managedImage->getDescription());
    self::assertSame($createdBy, $managedImage->getCreatedBy());
    self::assertSame($sourceImage, $managedImage->getSourceImage());
    self::assertSame(ManagedImageTypeEnum::THUMBNAIL, $managedImage->getType());
    self::assertSame(12, $managedImage->getId());
    self::assertNull($managedImage->getDerivative('missing'));
});

test('setters update mutable state', function () {
    $managedImage = ($this->makeManagedImage)();
    $updatedSourceImage = ($this->makeManagedFile)(['path' => 'test/updated-source.webp']);

    $managedImage->setDescription('Updated description');
    $managedImage->setSourceImage($updatedSourceImage);
    $managedImage->setType(ManagedImageTypeEnum::AVATAR_THUMBNAIL);

    self::assertSame('Updated description', $managedImage->getDescription());
    self::assertSame($updatedSourceImage, $managedImage->getSourceImage());
    self::assertSame(ManagedImageTypeEnum::AVATAR_THUMBNAIL, $managedImage->getType());
});

test('add derivative attaches derivative to image and allows lookup by format handle', function () {
    $managedImage = ($this->makeManagedImage)();
    $derivative = new ManagedImageDerivative(
        parentImage: $this->createStub(ManagedImage::class),
        format: ($this->makeManagedImageDerivativeFormat)(['handle' => 'card']),
        derivative: ($this->makeManagedFile)(['path' => 'test/card.webp']),
    );

    $managedImage->addDerivative($derivative);

    self::assertSame($managedImage, $derivative->getParentImage());
    self::assertSame($derivative, $managedImage->getDerivative('card'));
});

test('add derivative rejects duplicate format handles', function () {
    $managedImage = ($this->makeManagedImage)();
    $managedImage->addDerivative(new ManagedImageDerivative(
        parentImage: $managedImage,
        format: ($this->makeManagedImageDerivativeFormat)(['handle' => 'thumbnail']),
        derivative: ($this->makeManagedFile)(['path' => 'test/thumbnail.webp']),
    ));

    $this->expectException(DuplicateManagedImageDerivativeFormatException::class);
    $this->expectExceptionMessage(
        'A managed image derivative with format "thumbnail" already exists for this image.'
    );

    $managedImage->addDerivative(new ManagedImageDerivative(
        parentImage: $managedImage,
        format: ($this->makeManagedImageDerivativeFormat)([
            'handle' => 'thumbnail',
            'resolutionX' => 640,
            'resolutionY' => 360,
        ]),
        derivative: ($this->makeManagedFile)(['path' => 'test/thumbnail-2.webp']),
    ));
});
