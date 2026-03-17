<?php

declare(strict_types=1);

use App\Services\BugReport\Domain\BugReport;

test('construct', function () {
    $actionedAt = new DateTimeImmutable('2026-03-16 10:00:00');
    $closedAt = new DateTimeImmutable('2026-03-16 11:00:00');

    $bugReport = new BugReport(
        summary: 'Broken button',
        reporterName: 'Jane Doe',
        reporterEmail: 'jane@example.com',
        text: 'Clicking submit does nothing.',
        referringUrl: 'https://example.com/form',
        actionedAt: $actionedAt,
        closedAt: $closedAt,
        id: 15,
    );

    self::assertSame('Broken button', $bugReport->getSummary());
    self::assertSame('Jane Doe', $bugReport->getReporterName());
    self::assertSame('jane@example.com', $bugReport->getReporterEmail());
    self::assertSame('Clicking submit does nothing.', $bugReport->getText());
    self::assertSame('https://example.com/form', $bugReport->getReferringUrl());
    self::assertSame($actionedAt, $bugReport->getActionedAt());
    self::assertSame($closedAt, $bugReport->getClosedAt());
    self::assertSame(15, $bugReport->getId());
});

test('setters and lifecycle methods update state', function () {
    $bugReport = new BugReport(
        summary: 'Broken button',
        reporterName: 'Jane Doe',
        reporterEmail: 'jane@example.com',
        text: 'Clicking submit does nothing.',
    );

    $bugReport->setSummary('Broken modal');
    $bugReport->setReporterName('John Doe');
    $bugReport->setReporterEmail('john@example.com');
    $bugReport->setText('Opening the modal throws an error.');
    $bugReport->setReferringUrl('https://example.com/modal');
    $bugReport->markActioned();
    $bugReport->close();

    self::assertSame('Broken modal', $bugReport->getSummary());
    self::assertSame('John Doe', $bugReport->getReporterName());
    self::assertSame('john@example.com', $bugReport->getReporterEmail());
    self::assertSame('Opening the modal throws an error.', $bugReport->getText());
    self::assertSame('https://example.com/modal', $bugReport->getReferringUrl());
    self::assertTrue($bugReport->isActioned());
    self::assertTrue($bugReport->isClosed());
    self::assertInstanceOf(DateTimeImmutable::class, $bugReport->getActionedAt());
    self::assertInstanceOf(DateTimeImmutable::class, $bugReport->getClosedAt());
});
