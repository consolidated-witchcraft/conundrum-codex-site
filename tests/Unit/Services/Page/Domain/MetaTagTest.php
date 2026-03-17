<?php

declare(strict_types=1);

use App\Services\Page\Domain\MetaTag;

test('construct', function () {
    $metaTag = new MetaTag(
        name: 'description',
        content: 'A useful page description',
    );

    self::assertSame('description', $metaTag->name);
    self::assertSame('A useful page description', $metaTag->content);
});
