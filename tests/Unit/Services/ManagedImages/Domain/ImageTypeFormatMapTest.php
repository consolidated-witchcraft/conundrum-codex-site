<?php

declare(strict_types=1);

use App\Services\ManagedImages\Domain\Enums\ManagedImageTypeEnum;
use App\Services\ManagedImages\Domain\ImageTypeFormatMap;
use App\Services\ManagedImages\Domain\ManagedImageDerivativeFormat;
use Doctrine\Common\Collections\ArrayCollection;

test('construct', function () {
    $thumbnail = new ManagedImageDerivativeFormat(
        handle: 'thumbnail',
        resolutionX: 320,
        resolutionY: 180,
        quality: 85,
        id: 10,
    );
    $avatarThumbnail = new ManagedImageDerivativeFormat(
        handle: 'avatar-thumbnail',
        resolutionX: 128,
        resolutionY: 128,
        quality: 80,
        id: 11,
    );
    $formats = new ArrayCollection([$thumbnail, $avatarThumbnail]);

    $map = new ImageTypeFormatMap(
        managedImageType: ManagedImageTypeEnum::AVATAR_THUMBNAIL,
        formats: $formats,
        id: 3,
    );

    self::assertSame(ManagedImageTypeEnum::AVATAR_THUMBNAIL, $map->getManagedImageType());
    self::assertSame($formats, $map->getFormats());
    self::assertSame([$thumbnail, $avatarThumbnail], $map->getFormats()->toArray());
    self::assertSame(3, $map->getId());
});
