<?php

declare(strict_types=1);

use App\Services\ManagedFiles\Domain\ManagedFile;
use App\Services\User\Domain\User;

beforeEach(function () {
    $this->makeManagedFile = function (array $overrides = []): ManagedFile {
        return new ManagedFile(
            path: $overrides['path'] ?? 'test/path',
            fileHash: $overrides['fileHash'] ?? null,
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
        fileHash: '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef',
        mimeType: $mimeType,
        title: $title,
        description: "Test Description",
        uploader: $this->createStub(User::class),
    );
    expect($file->getPath())->toEqual($path);
    expect($file->getFileHash())->toEqual('0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef');
    expect($file->getMimeType())->toEqual($mimeType);
    expect($file->getTitle())->toEqual($title);
});

test('set file hash', function () {
    $newFileHash = 'abcdefabcdefabcdefabcdefabcdefabcdefabcdefabcdefabcdefabcdefabcd';
    $managedFile = ($this->makeManagedFile)();
    self::assertNull($managedFile->getFileHash());
    $managedFile->setFileHash($newFileHash);
    self::assertEquals($newFileHash, $managedFile->getFileHash());
});

test('set mime type', function () {
    $newMimeType = "image/png";
    $managedFile = ($this->makeManagedFile)();
    self::assertNotEquals($newMimeType, $managedFile->getMimeType());
    $managedFile->setMimeType($newMimeType);
    self::assertEquals($newMimeType, $managedFile->getMimeType());
});
