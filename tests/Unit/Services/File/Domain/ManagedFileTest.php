<?php

declare(strict_types=1);

use App\Services\Files\Domain\ManagedFile;
use App\Services\User\Domain\User;

beforeEach(function () {
    $this->makeManagedFile = function (array $overrides = []): ManagedFile {
        return new ManagedFile(
            path: $overrides['path'] ?? 'test/path',
            mimeType: $overrides['mimeType'] ?? 'text/plain',
            title: $overrides['title'] ?? "Test Title",
            description: $overrides['description'] ?? "Test Description",
            uploader: $overrides['uploader'] ?? $this->createStub(User::class),
        );
    };
});

test('construct', function () {
    $path = "test/path";
    $mimeType = "application/pdf";
    $title = "Test Title";
    $file = new ManagedFile(
        path: $path,
        mimeType: $mimeType,
        title: $title,
        description: "Test Description",
        uploader: $this->createStub(User::class),
    );
    expect($file->getPath())->toEqual($path);
    expect($file->getMimeType())->toEqual($mimeType);
    expect($file->getTitle())->toEqual($title);
});

test('set mime type', function () {
    $newMimeType = "image/png";
    $managedFile = ($this->makeManagedFile)();
    self::assertNotEquals($newMimeType, $managedFile->getMimeType());
    $managedFile->setMimeType($newMimeType);
    self::assertEquals($newMimeType, $managedFile->getMimeType());
});
