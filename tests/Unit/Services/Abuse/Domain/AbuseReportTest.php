<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services\Abuse\Domain;

use App\Services\Abuse\Domain\AbuseReport;
use App\Services\User\Domain\User;
use PHPUnit\Framework\TestCase;
use function Symfony\Component\String\s;

class AbuseReportTest extends TestCase
{

    private function generateTestAbuseReport(array $overrides = []): AbuseReport {
        return new AbuseReport(
            reportedUser: $overrides['reportedUser'] ?? $this->createStub(User::class),
            reason: $overrides['reason'] ?? 'Test Reason',
            notes: $overrides['notes'] ?? 'Test Notes',
            checkedDate: $overrides['checkedDate'] ?? null,
            confirmedDate: $overrides['confirmedDate'] ?? null,
            reportingUser: $overrides['reportingUser'],
            id: $overrides['id'] ?? null,
        );
    }
    public function testConstruct(): void {
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
    }

    public function testSetNotes(): void {
        $testReport = $this->generateTestAbuseReport();
        $testUpdatedNotes = "Test Updated Notes";
        self::assertNotEquals($testUpdatedNotes, $testReport->getNotes());
        $testReport->setNotes($testUpdatedNotes);
        self::assertEquals($testUpdatedNotes, $testReport->getNotes());
    }

    public function testMarkConfirmed(): void {
        $abuseReport = $this->generateTestAbuseReport();
        self::assertNull($abuseReport->getConfirmedDate());
        $abuseReport->markConfirmed();
        self::assertNotNull($abuseReport->getConfirmedDate());
    }

    public function testMarkChecked(): void {
        $abuseReport = $this->generateTestAbuseReport();
        self::assertNull($abuseReport->getCheckedDate());
        $abuseReport->markChecked();
        self::assertNotNull($abuseReport->getCheckedDate());
    }

    public function testSetReportingUser(): void {
        $abuseReport = $this->generateTestAbuseReport(['reportingUser' => null]);
        self::assertNull($abuseReport->getReportingUser());
        $reportingUser = $this->createStub(User::class);
        $abuseReport->setReportingUser($reportingUser);
        self::assertEquals($reportingUser, $abuseReport->getReportingUser());
    }

    public function testSetReason(): void {
        $abuseReport = $this->generateTestAbuseReport();
        $newReason = "Test New Reason";
        self::assertNotEquals($abuseReport->getReason(), $newReason);
        $abuseReport->setReason($newReason);
        self::assertEquals($newReason, $abuseReport->getReason());

    }
}
