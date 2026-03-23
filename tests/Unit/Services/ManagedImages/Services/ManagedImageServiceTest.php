<?php

declare(strict_types=1);

use App\Message\ManagedImageVariantsUpdateMessage;
use App\Services\ManagedFiles\Domain\ManagedFile;
use App\Services\ManagedFiles\Service\Interfaces\ManagedFileServiceInterface;
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\ManagedImages\Domain\ManagedImageDerivative;
use App\Services\ManagedImages\Domain\ManagedImageDerivativeFormat;
use App\Services\ManagedImages\Domain\Enums\ManagedImageTypeEnum;
use App\Services\ManagedImages\Infrastructure\ManagedImageRepository;
use App\Services\ManagedImages\Services\ManagedImageService;
use App\Services\ManagedImages\Services\Exceptions\ManagedFileNotImageException;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

beforeEach(function () {
    $this->repository = $this->createMock(ManagedImageRepository::class);
    $this->managedFileService = $this->createMock(ManagedFileServiceInterface::class);
    $this->messageBus = $this->createMock(MessageBusInterface::class);
    $this->entityManager = $this->createMock(EntityManagerInterface::class);

    $this->makeManagedImageService = function (): ManagedImageService {
        return new ManagedImageService(
            managedImageRepository: $this->repository,
            managedFileService: $this->managedFileService,
            messageBus: $this->messageBus,
            publicFilesDirectory: $this->uploadsDirectory ?? '/tmp/uploads',
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

    $this->makeManagedImage = function (array $overrides = []): ManagedImage {
        return new ManagedImage(
            description: $overrides['description'] ?? 'Test managed image',
            createdBy: $overrides['createdBy'] ?? $this->createStub(User::class),
            sourceImage: $overrides['sourceImage'] ?? ($this->makeManagedFile)(),
            type: $overrides['type'] ?? ManagedImageTypeEnum::DEFAULT,
            derivatives: $overrides['derivatives'] ?? new ArrayCollection(),
            id: $overrides['id'] ?? 123,
        );
    };

    $this->makeFormat = function (array $overrides = []): ManagedImageDerivativeFormat {
        return new ManagedImageDerivativeFormat(
            handle: $overrides['handle'] ?? 'thumbnail',
            resolutionX: $overrides['resolutionX'] ?? 256,
            quality: $overrides['quality'] ?? 85,
            resolutionY: $overrides['resolutionY'] ?? null,
            allowCrop: $overrides['allowCrop'] ?? false,
            centreImage: $overrides['centreImage'] ?? false,
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
    self::assertSame(ManagedImageTypeEnum::DEFAULT, $managedImage->getType());
    self::assertNull($managedImage->getDerivative('thumbnail'));
    self::assertSame(1, $flushCount);
});

test('upload image uploads managed file and migrates it', function () {
    $uploadedFile = $this->createStub(UploadedFile::class);
    $managedFile = ($this->makeManagedFile)();
    $existingManagedImage = $this->createStub(ManagedImage::class);

    $this->managedFileService
        ->expects($this->once())
        ->method('generateFileHash')
        ->with($uploadedFile->getPathname())
        ->willReturn('new-upload-hash');
    $this->managedFileService
        ->expects($this->once())
        ->method('findOneByFileHashAndUploader')
        ->with('new-upload-hash', $managedFile->getUploader())
        ->willReturn(null);

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

    $this->entityManager->expects($this->once())->method('persist')
        ->willReturnCallback(function (ManagedImage $managedImage): void {
            $managedImage->setId(123);
        });
    $this->entityManager->expects($this->once())->method('flush');
    $this->messageBus->expects($this->once())
        ->method('dispatch')
        ->with($this->callback(function (object $message): bool {
            return $message instanceof ManagedImageVariantsUpdateMessage
                && $message->getManagedImageId() === 123;
        }))
        ->willReturn(new Envelope(new ManagedImageVariantsUpdateMessage(123)));

    $service = ($this->makeManagedImageService)();

    $managedImage = $service->uploadImage(
        file: $uploadedFile,
        title: 'Image title',
        description: 'Image description',
        uploader: $managedFile->getUploader(),
        type: ManagedImageTypeEnum::DEFAULT,
        destination: '/tmp/uploads',
        imageToReplace: $existingManagedImage,
    );

    self::assertSame($managedFile, $managedImage->getSourceImage());
    self::assertSame(ManagedImageTypeEnum::DEFAULT, $managedImage->getType());
});

test('upload image replacing an existing managed image invalidates stale derivatives before dispatch', function () {
    $tempDirectory = sys_get_temp_dir() . '/managed-image-service-' . uniqid();
    $this->uploadsDirectory = $tempDirectory . '/public/uploads';
    mkdir($this->uploadsDirectory . '/variants', 0775, true);
    $staleDerivativePath = $this->uploadsDirectory . '/variants/stale-thumbnail.png';
    file_put_contents($staleDerivativePath, 'stale');

    $uploadedFile = $this->createStub(UploadedFile::class);
    $uploader = $this->createStub(User::class);
    $managedFile = ($this->makeManagedFile)(['uploader' => $uploader]);
    $staleDerivativeFile = ($this->makeManagedFile)([
        'path' => '/uploads/variants/stale-thumbnail.png',
        'uploader' => $uploader,
    ]);
    $existingManagedImage = ($this->makeManagedImage)([
        'sourceImage' => ($this->makeManagedFile)([
            'path' => '/uploads/original.png',
            'uploader' => $uploader,
        ]),
        'derivatives' => new ArrayCollection([
            new ManagedImageDerivative(
                parentImage: $this->createStub(ManagedImage::class),
                format: ($this->makeFormat)(['handle' => 'thumbnail']),
                derivative: $staleDerivativeFile,
            ),
        ]),
        'id' => 123,
    ]);

    $this->managedFileService
        ->expects($this->once())
        ->method('generateFileHash')
        ->with($uploadedFile->getPathname())
        ->willReturn('new-upload-hash');
    $this->managedFileService
        ->expects($this->once())
        ->method('findOneByFileHashAndUploader')
        ->with('new-upload-hash', $uploader)
        ->willReturn(null);
    $this->managedFileService
        ->expects($this->once())
        ->method('uploadFile')
        ->willReturn($managedFile);

    $this->repository
        ->expects($this->once())
        ->method('findOneBySourceImage')
        ->with($managedFile)
        ->willReturn($existingManagedImage);

    $removedEntities = [];
    $flushCount = 0;
    $this->entityManager->expects($this->exactly(2))
        ->method('remove')
        ->willReturnCallback(function (object $entity) use (&$removedEntities): void {
            $removedEntities[] = $entity;
        });
    $this->entityManager->expects($this->once())
        ->method('flush')
        ->willReturnCallback(function () use (&$flushCount): void {
            ++$flushCount;
        });
    $this->entityManager->expects($this->never())->method('persist');
    $this->messageBus->expects($this->once())
        ->method('dispatch')
        ->with($this->callback(function (object $message): bool {
            return $message instanceof ManagedImageVariantsUpdateMessage
                && $message->getManagedImageId() === 123;
        }))
        ->willReturn(new Envelope(new ManagedImageVariantsUpdateMessage(123)));

    $service = ($this->makeManagedImageService)();

    try {
        $managedImage = $service->uploadImage(
            file: $uploadedFile,
            title: 'Image title',
            description: 'Image description',
            uploader: $uploader,
            imageToReplace: $existingManagedImage,
        );
    } finally {
        @rmdir($this->uploadsDirectory . '/variants');
        @rmdir($this->uploadsDirectory);
        @rmdir(dirname($this->uploadsDirectory));
        @rmdir($tempDirectory);
    }

    self::assertSame($existingManagedImage, $managedImage);
    self::assertCount(2, $removedEntities);
    self::assertSame($staleDerivativeFile, $removedEntities[1]);
    self::assertNull($existingManagedImage->getDerivative('thumbnail'));
    self::assertSame(1, $flushCount);
    self::assertFileDoesNotExist($staleDerivativePath);
});

test('upload image reuses existing managed file and managed image for same uploader and hash', function () {
    $uploadedFile = $this->createMock(UploadedFile::class);
    $uploader = $this->createStub(User::class);
    $existingManagedFile = ($this->makeManagedFile)([
        'uploader' => $uploader,
        'fileHash' => 'matching-hash',
    ]);
    $existingManagedImage = $this->createStub(ManagedImage::class);

    $uploadedFile->method('getPathname')->willReturn('/tmp/uploaded-image.png');

    $this->managedFileService
        ->expects($this->once())
        ->method('generateFileHash')
        ->with('/tmp/uploaded-image.png')
        ->willReturn('matching-hash');
    $this->managedFileService
        ->expects($this->once())
        ->method('findOneByFileHashAndUploader')
        ->with('matching-hash', $uploader)
        ->willReturn($existingManagedFile);
    $this->managedFileService->expects($this->never())->method('uploadFile');

    $this->repository
        ->expects($this->once())
        ->method('findOneBySourceImage')
        ->with($existingManagedFile)
        ->willReturn($existingManagedImage);

    $this->entityManager->expects($this->never())->method('persist');
    $this->entityManager->expects($this->never())->method('flush');
    $this->messageBus->expects($this->never())->method('dispatch');

    $service = ($this->makeManagedImageService)();

    self::assertSame(
        $existingManagedImage,
        $service->uploadImage(
            file: $uploadedFile,
            title: 'Image title',
            description: 'Image description',
            uploader: $uploader,
            type: ManagedImageTypeEnum::DEFAULT,
        ),
    );
});

test('upload image assigns the requested managed image type before dispatch', function () {
    $uploadedFile = $this->createStub(UploadedFile::class);
    $managedFile = ($this->makeManagedFile)();

    $this->managedFileService
        ->expects($this->once())
        ->method('generateFileHash')
        ->with($uploadedFile->getPathname())
        ->willReturn('avatar-upload-hash');
    $this->managedFileService
        ->expects($this->once())
        ->method('findOneByFileHashAndUploader')
        ->with('avatar-upload-hash', $managedFile->getUploader())
        ->willReturn(null);
    $this->managedFileService
        ->expects($this->once())
        ->method('uploadFile')
        ->willReturn($managedFile);

    $this->repository
        ->expects($this->once())
        ->method('findOneBySourceImage')
        ->with($managedFile)
        ->willReturn(null);

    $this->entityManager->expects($this->once())->method('persist')
        ->willReturnCallback(function (ManagedImage $managedImage): void {
            $managedImage->setId(456);
        });
    $this->entityManager->expects($this->once())->method('flush');
    $this->messageBus->expects($this->once())
        ->method('dispatch')
        ->with($this->callback(function (object $message): bool {
            return $message instanceof ManagedImageVariantsUpdateMessage
                && $message->getManagedImageId() === 456;
        }))
        ->willReturn(new Envelope(new ManagedImageVariantsUpdateMessage(456)));

    $service = ($this->makeManagedImageService)();

    $managedImage = $service->uploadImage(
        file: $uploadedFile,
        title: 'Avatar image',
        description: 'Avatar description',
        uploader: $managedFile->getUploader(),
        type: ManagedImageTypeEnum::AVATAR,
    );

    self::assertSame(ManagedImageTypeEnum::AVATAR, $managedImage->getType());
});

test('generate missing variants rejects non-image source files', function () {
    $managedImage = ($this->makeManagedImage)([
        'sourceImage' => ($this->makeManagedFile)(['mimeType' => 'application/pdf']),
    ]);

    $service = ($this->makeManagedImageService)();

    $this->expectException(ManagedFileNotImageException::class);

    $service->generateMissingVariants($managedImage, [($this->makeFormat)()]);
});

test('generate missing variants creates derivative files and persists them', function () {
    $tempDirectory = sys_get_temp_dir() . '/managed-image-service-' . uniqid();
    $this->uploadsDirectory = $tempDirectory . '/public/uploads';
    mkdir($this->uploadsDirectory, 0775, true);

    $sourcePath = $this->uploadsDirectory . '/source.png';
    $sourceImage = new \Imagick();
    $sourceImage->newImage(80, 40, new \ImagickPixel('red'));
    $sourceImage->setImageFormat('png');
    $sourceImage->writeImage($sourcePath);
    $sourceImage->clear();
    $sourceImage->destroy();

    $managedImage = ($this->makeManagedImage)([
        'sourceImage' => ($this->makeManagedFile)(['path' => '/uploads/source.png']),
    ]);
    $format = ($this->makeFormat)([
        'handle' => 'card',
        'resolutionX' => 40,
        'resolutionY' => 40,
        'allowCrop' => true,
        'centreImage' => true,
    ]);

    $persistedEntities = [];
    $this->managedFileService->expects($this->once())
        ->method('generateFileHash')
        ->willReturnCallback(static fn (string $filePath): string => hash_file('sha256', $filePath));
    $this->entityManager->expects($this->exactly(2))
        ->method('persist')
        ->willReturnCallback(function (object $entity) use (&$persistedEntities): void {
            $persistedEntities[] = $entity;
        });
    $this->entityManager->expects($this->once())->method('flush');

    $service = ($this->makeManagedImageService)();

    $service->generateMissingVariants($managedImage, [$format]);

    self::assertCount(2, $persistedEntities);
    self::assertInstanceOf(ManagedFile::class, $persistedEntities[0]);
    self::assertInstanceOf(ManagedImageDerivative::class, $persistedEntities[1]);

    /** @var ManagedFile $derivativeFile */
    $derivativeFile = $persistedEntities[0];
    self::assertSame('/uploads/variants/source-card.webp', $derivativeFile->getPath());
    self::assertSame('image/webp', $derivativeFile->getMimeType());
    self::assertFileExists($tempDirectory . '/public' . $derivativeFile->getPath());
    self::assertSame($derivativeFile, $managedImage->getDerivative('card')?->getDerivative());

    array_map('unlink', glob($this->uploadsDirectory . '/variants/*') ?: []);
    @rmdir($this->uploadsDirectory . '/variants');
    @unlink($sourcePath);
    @rmdir($this->uploadsDirectory);
    @rmdir(dirname($this->uploadsDirectory));
    @rmdir($tempDirectory);
});

test('generate missing variants supports width-only resize formats', function () {
    $tempDirectory = sys_get_temp_dir() . '/managed-image-service-' . uniqid();
    $this->uploadsDirectory = $tempDirectory . '/public/uploads';
    mkdir($this->uploadsDirectory, 0775, true);

    $sourcePath = $this->uploadsDirectory . '/source.png';
    $sourceImage = new \Imagick();
    $sourceImage->newImage(1200, 600, new \ImagickPixel('blue'));
    $sourceImage->setImageFormat('png');
    $sourceImage->writeImage($sourcePath);
    $sourceImage->clear();
    $sourceImage->destroy();

    $managedImage = ($this->makeManagedImage)([
        'sourceImage' => ($this->makeManagedFile)(['path' => '/uploads/source.png']),
    ]);
    $format = ($this->makeFormat)([
        'handle' => 'default',
        'resolutionX' => 1024,
        'resolutionY' => null,
        'allowCrop' => false,
    ]);

    $persistedEntities = [];
    $this->managedFileService->expects($this->once())
        ->method('generateFileHash')
        ->willReturnCallback(static fn (string $filePath): string => hash_file('sha256', $filePath));
    $this->entityManager->expects($this->exactly(2))
        ->method('persist')
        ->willReturnCallback(function (object $entity) use (&$persistedEntities): void {
            $persistedEntities[] = $entity;
        });
    $this->entityManager->expects($this->once())->method('flush');

    $service = ($this->makeManagedImageService)();
    $service->generateMissingVariants($managedImage, [$format]);

    /** @var ManagedFile $derivativeFile */
    $derivativeFile = $persistedEntities[0];
    $generatedImage = new \Imagick($tempDirectory . '/public' . $derivativeFile->getPath());

    self::assertSame('/uploads/variants/source-default.webp', $derivativeFile->getPath());
    self::assertSame('image/webp', $derivativeFile->getMimeType());
    self::assertSame(1024, $generatedImage->getImageWidth());
    self::assertSame(512, $generatedImage->getImageHeight());

    $generatedImage->clear();
    $generatedImage->destroy();
    array_map('unlink', glob($this->uploadsDirectory . '/variants/*') ?: []);
    @rmdir($this->uploadsDirectory . '/variants');
    @unlink($sourcePath);
    @rmdir($this->uploadsDirectory);
    @rmdir(dirname($this->uploadsDirectory));
    @rmdir($tempDirectory);
});
