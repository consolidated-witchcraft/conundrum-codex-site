<?php

declare(strict_types=1);

use App\Message\ManagedImageVariantsUpdateMessage;

test('construct', function () {
    $message = new ManagedImageVariantsUpdateMessage(42);

    self::assertSame(42, $message->getManagedImageId());
});
