<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services\User\Domain;

use App\Services\Core\Domain\Exceptions\InvalidHandleException;
use App\Services\User\Domain\Exceptions\InvalidRoleHandleException;
use App\Services\User\Domain\Permission;
use App\Services\User\Domain\Role;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{

    /**
     * @throws InvalidHandleException
     * @throws InvalidRoleHandleException
     */
    private function generateTestRole(array $overrides = []): Role {
        return new Role(
            label: $overrides['name'] ?? 'Test Name',
            handle: $overrides['handle'] ?? 'ROLE_TEST',
            permissions: $overrides['permissions'] ?? new ArrayCollection(),
            users: $overrides['users'] ?? new ArrayCollection(),
            id: $overrides['id'] ?? null
        );
    }
    public function testConstruct(): void {
        $name = "Test name";
        $handle = "ROLE_TEST";
        $users = new ArrayCollection([
            $this->createStub(User::class)
        ]);
        $permissions = new ArrayCollection([
            $this->createStub(Permission::class)
        ]);

        $id = 1;

        $role = $this->generateTestRole([
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
    }

    /**
     * @throws Exception
     */
    public function testAddPermission(): void {
        $role = $this->generateTestRole();
        self::assertCount(0, $role->getPermissions());
        $role->addPermission($this->createStub(Permission::class));
        self::assertCount(1, $role->getPermissions());
    }

    public function testRemovePermission(): void {
        $role = $this->generateTestRole();
        $permission = $this->createStub(Permission::class);
        $role->addPermission($permission);
        $role->removePermission($permission);
        self::assertCount(0, $role->getPermissions());
    }

    public function testHasPermission(): void {

        $permissions = new ArrayCollection([
           new Permission(
               label:'Test Label',
               handle: 'test_permission',
               description: 'Test Description'

           )
        ]);

        $role = $this->generateTestRole(
            [
                'permissions' => $permissions
            ]
        );

        self::assertTrue($role->hasPermission('test_permission'));
        self::assertFalse($role->hasPermission('none_existant_permission'));
    }

    public function testInvalidConstruct(): void {
        $this->expectException(InvalidRoleHandleException::class);
        $role = $this->generateTestRole(['handle' => 'TEST_ROLE']);
    }
}
