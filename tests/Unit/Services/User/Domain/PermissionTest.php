<?php

declare(strict_types=1);

use App\Services\Core\Domain\AbstractValueObject;
use App\Services\User\Domain\Permission;


test('construct', function () {
    $label = 'Test Label';
    $handle = 'test_handle';
    $description = 'Test Description';
    $id = 1;
    $permission = new Permission(
        label: $label,
        handle: $handle,
        description: $description,
        id: $id
    );
    self::assertEquals($label, $permission->getLabel());
    self::assertEquals($handle, $permission->getHandle());
    self::assertEquals($description, $permission->getDescription());
    self::assertEquals($id, $permission->getId());
});
function generateTestValueObject(array $overrides = []): AbstractValueObject
{
    return new Permission(
        label: $overrides['label'] ?? 'Test Label',
        handle: $overrides['handle'] ?? 'test_handle',
        description: $overrides['description'] ?? 'Test Description',
        id: $overrides['id'] ?? null
    );
}
