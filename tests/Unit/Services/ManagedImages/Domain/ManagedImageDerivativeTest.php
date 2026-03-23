<?php

declare(strict_types=1);

use App\Services\ManagedFiles\Domain\ManagedFile;
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\ManagedImages\Domain\ManagedImageDerivative;
use App\Services\ManagedImages\Domain\ManagedImageDerivativeFormat;
use App\Services\User\Domain\User;

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

    $this->makeManagedImageDerivativeFormat = function (array $overrides = []): ManagedImageDerivativeFormat {
        return new ManagedImageDerivativeFormat(
            handle: $overrides['handle'] ?? 'thumbnail',
            resolutionX: $overrides['resolutionX'] ?? 320,
            resolutionY: $overrides['resolutionY'] ?? 180,
            quality: $overrides['quality'] ?? 85,
            allowCrop: $overrides['allowCrop'] ?? false,
            centreImage: $overrides['centreImage'] ?? false,
            id: $overrides['id'] ?? null,
        );
    };
});

test('construct', function () {
    $parentImage = $this->createStub(ManagedImage::class);
    $format = ($this->makeManagedImageDerivativeFormat)(['handle' => 'hero']);
    $derivativeFile = ($this->makeManagedFile)(['path' => 'test/hero.webp']);
    $derivative = new ManagedImageDerivative(
        parentImage: $parentImage,
        format: $format,
        derivative: $derivativeFile,
        id: 7,
    );

    self::assertSame($parentImage, $derivative->getParentImage());
    self::assertSame($format, $derivative->getDerivativeFormat());
    self::assertSame($derivativeFile, $derivative->getDerivative());
    self::assertSame(7, $derivative->getId());
});

test('setters update parent image and derivative file', function () {
    $derivative = new ManagedImageDerivative(
        parentImage: $this->createStub(ManagedImage::class),
        format: ($this->makeManagedImageDerivativeFormat)(),
        derivative: ($this->makeManagedFile)(['path' => 'test/original.webp']),
    );
    $updatedParentImage = $this->createStub(ManagedImage::class);
    $updatedDerivativeFile = ($this->makeManagedFile)(['path' => 'test/updated.webp']);

    $derivative->setParentImage($updatedParentImage);
    $derivative->setDerivative($updatedDerivativeFile);

    self::assertSame($updatedParentImage, $derivative->getParentImage());
    self::assertSame($updatedDerivativeFile, $derivative->getDerivative());
});
