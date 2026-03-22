<?php

declare(strict_types=1);

use App\Services\ManagedFiles\Domain\ManagedFile;
use App\Services\ManagedFiles\Service\Interfaces\ManagedFileServiceInterface;
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\ManagedImages\Infrastructure\ManagedImageRepository;
use App\Services\ManagedImages\Services\ManagedImageService;
use App\Services\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

beforeEach(function () {
    $this->repository = $this->createMock(ManagedImageRepository::class);
    $this->managedFileService = $this->createMock(ManagedFileServiceInterface::class);
    $this->entityManager = $this->createMock(EntityManagerInterface::class);

    $this->makeManagedImageService = function (): ManagedImageService {
        return new ManagedImageService(
            managedImageRepository: $this->repository,
            managedFileService: $this->managedFileService,
            entityManager: $this->entityManager,
        );
    };

    $this->makeManagedFile = function (array $overrides = []): ManagedFile {
        return new ManagedFile(
            path: $overrides['path'] ?? '/uploads/test-image.png',
            mimeType: $overrides['mimeType'] ?? 'image/png',
            title: $overrides['title'] ?? 'Test image',
            description: $overrides['description'] ?? 'Test description',
            uploader: $overrides['uploader'] ?? $this->createStub(User::class),
            fileHash: $overrides['fileHash'] ?? '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef',
            id: $overrides['id'] ?? null,
        );
    };
});

test('find by source image delegates to repository', function () {
    $managedFile = ($this->makeManagedFile)();
    $managedImage = $this->createStub(ManagedImage::class);
    $this->repository
        ->expects($this->once())
        ->method('findOneBySourceImage')
        ->with($managedFile)
        ->willReturn($managedImage);

    $service = ($this->makeManagedImageService)();

    self::assertSame($managedImage, $service->findBySourceImage(managedFile: $managedFile));
});

test('migrate managed file returns existing managed image when one already exists', function () {
    $managedFile = ($this->makeManagedFile)();
    $managedImage = $this->createStub(ManagedImage::class);
    $this->repository
        ->expects($this->once())
        ->method('findOneBySourceImage')
        ->with($managedFile)
        ->willReturn($managedImage);
    $this->entityManager->expects($this->never())->method('persist');
    $this->entityManager->expects($this->never())->method('flush');

    $service = ($this->makeManagedImageService)();

    self::assertSame($managedImage, $service->migrateManagedFile(managedFile: $managedFile));
});

test('migrate managed file creates and saves managed image when one does not exist', function () {
    $managedFile = ($this->makeManagedFile)([
        'description' => 'Managed file description',
    ]);
    $this->repository
        ->expects($this->once())
        ->method('findOneBySourceImage')
        ->with($managedFile)
        ->willReturn(null);

    $persistedImage = null;
    $flushCount = 0;

    $this->entityManager->expects($this->once())->method('persist')
        ->willReturnCallback(function (ManagedImage $managedImage) use (&$persistedImage): void {
            $persistedImage = $managedImage;
        });
    $this->entityManager->expects($this->once())->method('flush')
        ->willReturnCallback(function () use (&$flushCount): void {
            ++$flushCount;
        });

    $service = ($this->makeManagedImageService)();

    $managedImage = $service->migrateManagedFile(managedFile: $managedFile);

    self::assertInstanceOf(ManagedImage::class, $managedImage);
    self::assertSame($managedImage, $persistedImage);
    self::assertSame('Managed file description', $managedImage->getDescription());
    self::assertSame($managedFile->getUploader(), $managedImage->getCreatedBy());
    self::assertSame($managedFile, $managedImage->getSourceImage());
    self::assertNull($managedImage->getDerivative('thumbnail'));
    self::assertSame(1, $flushCount);
});

test('upload image uploads managed file and migrates it', function () {
    $uploadedFile = $this->createStub(UploadedFile::class);
    $managedFile = ($this->makeManagedFile)();
    $existingManagedImage = $this->createStub(ManagedImage::class);

    $this->managedFileService
        ->expects($this->once())
        ->method('uploadFile')
        ->with(
            file: $uploadedFile,
            title: 'Image title',
            description: 'Image description',
            uploader: $managedFile->getUploader(),
            destination: '/tmp/uploads',
            fileToReplace: $existingManagedImage->getSourceImage(),
        )
        ->willReturn($managedFile);

    $this->repository
        ->expects($this->once())
        ->method('findOneBySourceImage')
        ->with($managedFile)
        ->willReturn(null);

    $this->entityManager->expects($this->once())->method('persist');
    $this->entityManager->expects($this->once())->method('flush');

    $service = ($this->makeManagedImageService)();

    $managedImage = $service->uploadImage(
        file: $uploadedFile,
        title: 'Image title',
        description: 'Image description',
        uploader: $managedFile->getUploader(),
        destination: '/tmp/uploads',
        imageToReplace: $existingManagedImage,
    );

    self::assertSame($managedFile, $managedImage->getSourceImage());
});
