<?php

declare(strict_types=1);
use App\Services\Core\Domain\Exceptions\InvalidHandleException;
use App\Services\User\Domain\Exceptions\InvalidRoleHandleException;
use App\Services\User\Domain\Permission;
use App\Services\User\Domain\Role;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\Exception;
/**
 * @throws InvalidHandleException
 * @throws InvalidRoleHandleException
 */
function generateTestRole(array $overrides = []): Role
{
    return new Role(
        label: $overrides['name'] ?? 'Test Name',
        handle: $overrides['handle'] ?? 'ROLE_TEST',
        permissions: $overrides['permissions'] ?? new ArrayCollection(),
        users: $overrides['users'] ?? new ArrayCollection(),
        id: $overrides['id'] ?? null
    );
}
test('construct', function () {
    $name = "Test name";
    $handle = "ROLE_TEST";
    $users = new ArrayCollection([
        $this->createStub(User::class)
    ]);
    $permissions = new ArrayCollection([
        $this->createStub(Permission::class)
    ]);

    $id = 1;

    $role = generateTestRole([
        'name' => $name,
        'handle' => $handle,
        'users' => $users,
        'permissions' => $permissions,
        'id' => $id
    ]);
    self::assertEquals($name, $role->getLabel());
    self::assertEquals($handle, $role->getHandle());
    self::assertEquals($permissions, $role->getPermissions());
    self::assertEquals($users, $role->getUsers());
    self::assertEquals($id, $role->getId());
});
test('add permission', function () {
    $role = generateTestRole();
    self::assertCount(0, $role->getPermissions());
    $role->addPermission($this->createStub(Permission::class));
    self::assertCount(1, $role->getPermissions());
});
test('remove permission', function () {
    $role = generateTestRole();
    $permission = $this->createStub(Permission::class);
    $role->addPermission($permission);
    $role->removePermission($permission);
    self::assertCount(0, $role->getPermissions());
});
test('has permission', function () {
    $permissions = new ArrayCollection([
       new Permission(
           label:'Test Label',
           handle: 'test_permission',
           description: 'Test Description'

       )
    ]);

    $role = generateTestRole([
        'permissions' => $permissions
    ]);

    self::assertTrue($role->hasPermission('test_permission'));
    self::assertFalse($role->hasPermission('none_existant_permission'));
});
test('invalid construct', function () {
    $this->expectException(InvalidRoleHandleException::class);
    $role = generateTestRole(['handle' => 'TEST_ROLE']);
});
