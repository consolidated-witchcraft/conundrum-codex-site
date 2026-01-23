<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services\File\Domain;

use App\Services\Files\Domain\ManagedFile;
use App\Services\User\Domain\User;
use PHPUnit\Framework\TestCase;

class ManagedFileTest extends TestCase
{

    private function generateTestFile(array $overrides = []): ManagedFile {
        return new ManagedFile(
            path: $overrides['path'] ?? 'test/path',
            mimeType: $overrides['mimeType'] ?? 'text/plain',
            title: $overrides['title'] ?? "Test Title",
            description: $overrides['description'] ?? "Test Description",
            uploader: $overrides['uploader'] ?? $this->createMock(User::class),
        );
    }

    public function testConstruct(): void {
        $path = "test/path";
        $mimeType = "application/pdf";
        $title = "Test Title";
        $file = new ManagedFile(
            path: $path,
            mimeType: $mimeType,
            title: $title,
            description: "Test Description",
            uploader: $this->createMock(User::class),
        );
        $this->assertEquals($path, $file->getPath());
        $this->assertEquals($mimeType, $file->getMimeType());
        $this->assertEquals($title, $file->getTitle());
    }

    public function testSetMimeType(): void {
        $newMimeType = "image/png";
        $managedFile = $this->generateTestFile();
        self::assertNotEquals($newMimeType, $managedFile->getMimeType());
        $managedFile->setMimeType($newMimeType);
        self::assertEquals($newMimeType, $managedFile->getMimeType());
    }
}
