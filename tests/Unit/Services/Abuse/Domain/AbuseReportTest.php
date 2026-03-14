<?php

declare(strict_types=1);

use App\Services\Abuse\Domain\AbuseReport;
use App\Services\User\Domain\User;

beforeEach(function () {
    $this->makeAbuseReport = function (array $overrides = []): AbuseReport {
        return new AbuseReport(
            reportedUser: $overrides['reportedUser'] ?? $this->createStub(User::class),
            reason: $overrides['reason'] ?? 'Test Reason',
            notes: $overrides['notes'] ?? 'Test Notes',
            checkedDate: $overrides['checkedDate'] ?? null,
            confirmedDate: $overrides['confirmedDate'] ?? null,
            reportingUser: $overrides['reportingUser'] ?? null,
            id: $overrides['id'] ?? null,
        );
    };
});

test('construct', function () {
    $reportedUser = $this->createStub(User::class);
    $reportingUser = $this->createStub(User::class);
    $reason = "Test Reason";
    $notes = "Test Notes";
    $checkedDate = new \DateTimeImmutable();
    $confirmedDate = new \DateTimeImmutable();
    $id = 123;
    $abuseReport = new AbuseReport(
        reportedUser: $reportedUser,
        reason: $reason,
        notes: $notes,
        checkedDate: $checkedDate,
        confirmedDate: $confirmedDate,
        reportingUser: $reportingUser,
        id: $id
    );
    self::assertEquals($reportedUser, $abuseReport->getReportedUser());
    self::assertEquals($reason, $abuseReport->getReason());
    self::assertEquals($notes, $abuseReport->getNotes());
    self::assertEquals($checkedDate, $abuseReport->getCheckedDate());
    self::assertEquals($confirmedDate, $abuseReport->getConfirmedDate());
    self::assertEquals($reportingUser, $abuseReport->getReportingUser());
    self::assertEquals($id, $abuseReport->getId());
});

test('set notes', function () {
    $testReport = ($this->makeAbuseReport)();
    $testUpdatedNotes = "Test Updated Notes";
    self::assertNotEquals($testUpdatedNotes, $testReport->getNotes());
    $testReport->setNotes($testUpdatedNotes);
    self::assertEquals($testUpdatedNotes, $testReport->getNotes());
});

test('mark confirmed', function () {
    $abuseReport = ($this->makeAbuseReport)();
    self::assertNull($abuseReport->getConfirmedDate());
    $abuseReport->markConfirmed();
    self::assertNotNull($abuseReport->getConfirmedDate());
});

test('mark checked', function () {
    $abuseReport = ($this->makeAbuseReport)();
    self::assertNull($abuseReport->getCheckedDate());
    $abuseReport->markChecked();
    self::assertNotNull($abuseReport->getCheckedDate());
});

test('set reporting user', function () {
    $abuseReport = ($this->makeAbuseReport)(['reportingUser' => null]);
    self::assertNull($abuseReport->getReportingUser());
    $reportingUser = $this->createStub(User::class);
    $abuseReport->setReportingUser($reportingUser);
    self::assertEquals($reportingUser, $abuseReport->getReportingUser());
});

test('set reason', function () {
    $abuseReport = ($this->makeAbuseReport)();
    $newReason = "Test New Reason";
    self::assertNotEquals($abuseReport->getReason(), $newReason);
    $abuseReport->setReason($newReason);
    self::assertEquals($newReason, $abuseReport->getReason());
});
