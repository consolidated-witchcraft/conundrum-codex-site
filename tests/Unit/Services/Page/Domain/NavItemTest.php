<?php

declare(strict_types=1);

use App\Services\Page\Domain\NavItem;

test('construct', function () {
    $child = new NavItem(
        handle: 'child',
        label: 'Child',
        route: 'app.child',
    );

    $navItem = new NavItem(
        handle: 'home',
        label: 'Home',
        route: 'app.home',
        routeArguments: ['slug' => 'test'],
        children: [$child],
        isActive: true,
        isActiveTrail: true,
    );

    self::assertSame('home', $navItem->getHandle());
    self::assertSame('Home', $navItem->getLabel());
    self::assertSame('app.home', $navItem->getRoute());
    self::assertSame(['slug' => 'test'], $navItem->getRouteArguments());
    self::assertSame([$child], $navItem->getChildren());
    self::assertTrue($navItem->isActive());
    self::assertTrue($navItem->isActiveTrail());
});

test('mutators update state', function () {
    $navItem = new NavItem(
        handle: 'home',
        label: 'Home',
        route: 'app.home',
    );
    $child = new NavItem(
        handle: 'child',
        label: 'Child',
        route: 'app.child',
    );

    $navItem->setActive(true);
    $navItem->setActiveTrail(true);
    $navItem->addChild($child);

    self::assertTrue($navItem->isActive());
    self::assertTrue($navItem->isActiveTrail());
    self::assertSame([$child], $navItem->getChildren());
});
