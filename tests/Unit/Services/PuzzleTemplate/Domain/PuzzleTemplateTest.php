<?php

declare(strict_types=1);

use App\Services\PuzzleTemplate\Domain\ConfigOptionDefinitions\PuzzleTemplateConfigOptionDefinitionInterface;
use App\Services\PuzzleTemplate\Domain\Exceptions\NonStaticConfigurationAttemptException;
use App\Services\PuzzleTemplate\Domain\Exceptions\RoutelessStaticConfigurationAttemptException;
use App\Services\PuzzleTemplate\Domain\PuzzleCategory;
use App\Services\PuzzleTemplate\Domain\PuzzleCredit;
use App\Services\PuzzleTemplate\Domain\PuzzleTemplate;
use Doctrine\Common\Collections\ArrayCollection;

test('construct', function () {
    $createdAt = new DateTimeImmutable('2026-03-16 12:00:00');
    $categories = new ArrayCollection([
        new PuzzleCategory('Mystery', 'mystery', 'Mystery puzzles.'),
    ]);
    $credits = [new PuzzleCredit('Jane Doe', 'Writing', 'jane@example.com')];
    $configuration = [$this->createStub(PuzzleTemplateConfigOptionDefinitionInterface::class)];

    $puzzleTemplate = new PuzzleTemplate(
        slug: 'test-template',
        title: 'Test Template',
        createdAt: $createdAt,
        description: 'Template description',
        categories: $categories,
        authorEmail: 'author@example.com',
        static: true,
        elementName: 'test-template-element',
        credits: $credits,
        configuration: $configuration,
        staticCreateRoute: 'template_create',
        staticEditRoute: 'template_edit',
        staticPlayRoute: 'template_play',
    );

    self::assertSame('test-template', $puzzleTemplate->getSlug());
    self::assertSame('Test Template', $puzzleTemplate->getTitle());
    self::assertSame($createdAt, $puzzleTemplate->getCreatedAt());
    self::assertSame('Template description', $puzzleTemplate->getDescription());
    self::assertSame($categories, $puzzleTemplate->getCategories());
    self::assertSame($categories, $puzzleTemplate->getPuzzleCategories());
    self::assertSame('author@example.com', $puzzleTemplate->getAuthorEmail());
    self::assertTrue($puzzleTemplate->isStatic());
    self::assertSame('test-template-element', $puzzleTemplate->getElementName());
    self::assertSame($credits, $puzzleTemplate->getCredits());
    self::assertSame($configuration, $puzzleTemplate->getConfiguration());
    self::assertSame('template_create', $puzzleTemplate->getStaticCreateRoute());
    self::assertSame('template_edit', $puzzleTemplate->getStaticEditRoute());
    self::assertSame('template_play', $puzzleTemplate->getStaticPlayRoute());
});

test('non static template rejects static route access', function () {
    $puzzleTemplate = new PuzzleTemplate(
        slug: 'test-template',
        title: 'Test Template',
        createdAt: new DateTimeImmutable('2026-03-16 12:00:00'),
        description: 'Template description',
        categories: new ArrayCollection(),
        authorEmail: 'author@example.com',
        static: false,
        elementName: 'test-template-element',
    );

    $this->expectException(NonStaticConfigurationAttemptException::class);

    $puzzleTemplate->getStaticCreateRoute();
});

test('static template without routes is rejected', function () {
    $puzzleTemplate = new PuzzleTemplate(
        slug: 'test-template',
        title: 'Test Template',
        createdAt: new DateTimeImmutable('2026-03-16 12:00:00'),
        description: 'Template description',
        categories: new ArrayCollection(),
        authorEmail: 'author@example.com',
        static: true,
        elementName: 'test-template-element',
    );

    $this->expectException(RoutelessStaticConfigurationAttemptException::class);

    $puzzleTemplate->getStaticCreateRoute();
});
