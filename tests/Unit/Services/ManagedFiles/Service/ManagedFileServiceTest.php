<?php

declare(strict_types=1);

use App\Services\ManagedFiles\Domain\Exceptions\GeneralFileManagementException;
use App\Services\ManagedFiles\Domain\Exceptions\MissingUploadPrefixException;
use App\Services\ManagedFiles\Domain\Exceptions\UndeterminedMimeTypeException;
use App\Services\ManagedFiles\Domain\ManagedFile;
use App\Services\ManagedFiles\Infrastructure\ManagedFileRepository;
use App\Services\ManagedFiles\Service\Interfaces\ManagedFileDeduplicationServiceInterface;
use App\Services\ManagedFiles\Service\ManagedFileService;
use App\Services\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;

beforeEach(function () {
    $this->managedFileRepository = $this->createMock(ManagedFileRepository::class);
    $this->deduplicationService = $this->createMock(ManagedFileDeduplicationServiceInterface::class);
    $this->entityManager = $this->createMock(EntityManagerInterface::class);
    $this->slugger = $this->createMock(SluggerInterface::class);
    $this->slugger
        ->method('slug')
        ->willReturn(new UnicodeString('test-file'));

    $this->uploadsDirectory = sys_get_temp_dir().'/managed-file-service-'.uniqid('', true).'/uploads';

    if (!is_dir($this->uploadsDirectory)) {
        mkdir($this->uploadsDirectory, 0777, true);
    }

    $this->makeService = function (): ManagedFileService {
        return new ManagedFileService(
            managedFileRepository: $this->managedFileRepository,
            managedFileDeduplicationService: $this->deduplicationService,
            slugger: $this->slugger,
            publicFilesDirectory: $this->uploadsDirectory,
            entityManager: $this->entityManager,
        );
    };

    $this->makeManagedFile = function (array $overrides = []): ManagedFile {
        return new ManagedFile(
            path: $overrides['path'] ?? '/uploads/original.txt',
            mimeType: $overrides['mimeType'] ?? 'text/plain',
            title: $overrides['title'] ?? 'Original title',
            description: $overrides['description'] ?? 'Original description',
            uploader: $overrides['uploader'] ?? $this->createStub(User::class),
            fileHash: $overrides['fileHash'] ?? 'old-hash',
            id: $overrides['id'] ?? null,
        );
    };

    $this->makeUploadedFile = function (string $sourcePath, array $overrides = []): UploadedFile {
        $uploadedFile = $this->createMock(UploadedFile::class);
        $uploadedFile
            ->method('getMimeType')
            ->willReturn($overrides['mimeType'] ?? 'text/plain');
        $uploadedFile
            ->method('getClientOriginalName')
            ->willReturn($overrides['clientOriginalName'] ?? 'Test File.txt');
        $uploadedFile
            ->method('guessExtension')
            ->willReturn($overrides['guessExtension'] ?? 'txt');
        $uploadedFile
            ->method('move')
            ->willReturnCallback(function (string $destination, string $name) use ($sourcePath): File {
                if (!is_dir($destination)) {
                    mkdir($destination, 0777, true);
                }

                copy($sourcePath, $destination.'/'.$name);

                return new File($destination.'/'.$name);
            });

        return $uploadedFile;
    };
});

afterEach(function () {
    $root = dirname($this->uploadsDirectory);

    if (is_dir($root)) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                rmdir($item->getPathname());
                continue;
            }

            unlink($item->getPathname());
        }

        rmdir($root);
    }
});

test('save persists and flushes by default', function () {
    $managedFile = ($this->makeManagedFile)();
    $service = ($this->makeService)();

    $this->entityManager->expects($this->once())->method('persist')->with($managedFile);
    $this->entityManager->expects($this->once())->method('flush');

    self::assertSame($managedFile, $service->save(managedFile: $managedFile));
});

test('save can skip flush', function () {
    $managedFile = ($this->makeManagedFile)();
    $service = ($this->makeService)();

    $this->entityManager->expects($this->once())->method('persist')->with($managedFile);
    $this->entityManager->expects($this->never())->method('flush');

    self::assertSame($managedFile, $service->save(managedFile: $managedFile, flush: false));
});

test('delete removes and flushes by default', function () {
    $managedFile = ($this->makeManagedFile)();
    $service = ($this->makeService)();

    $this->entityManager->expects($this->once())->method('remove')->with($managedFile);
    $this->entityManager->expects($this->once())->method('flush');

    $service->delete(managedFile: $managedFile);
    expect(true)->toBeTrue();
});

test('delete can skip flush', function () {
    $managedFile = ($this->makeManagedFile)();
    $service = ($this->makeService)();

    $this->entityManager->expects($this->once())->method('remove')->with($managedFile);
    $this->entityManager->expects($this->never())->method('flush');

    $service->delete(managedFile: $managedFile, flush: false);
    expect(true)->toBeTrue();
});

test('find delegates to repository', function () {
    $managedFile = ($this->makeManagedFile)(['id' => 10]);
    $this->managedFileRepository->expects($this->once())->method('find')->with(10)->willReturn($managedFile);
    $service = ($this->makeService)();

    self::assertSame($managedFile, $service->find(id: 10));
});

test('find all paginated delegates to repository', function () {
    $expected = [($this->makeManagedFile)()];
    $this->managedFileRepository
        ->expects($this->once())
        ->method('findAllPaginated')
        ->with(5, 20)
        ->willReturn($expected);
    $service = ($this->makeService)();

    self::assertSame($expected, $service->findAllPaginated(firstResult: 5, maxResults: 20));
});

test('get files count delegates to repository', function () {
    $this->managedFileRepository->expects($this->once())->method('getFilesCount')->willReturn(12);
    $service = ($this->makeService)();

    self::assertSame(12, $service->getFilesCount());
});

test('find by mime types delegates to repository', function () {
    $expected = [($this->makeManagedFile)()];
    $this->managedFileRepository
        ->expects($this->once())
        ->method('findByMimeTypes')
        ->with(['image/png', 'image/jpeg'])
        ->willReturn($expected);
    $service = ($this->makeService)();

    self::assertSame($expected, $service->findByMimeTypes(mimeTypes: ['image/png', 'image/jpeg']));
});

test('find all delegates to repository', function () {
    $expected = [($this->makeManagedFile)()];
    $this->managedFileRepository->expects($this->once())->method('findAll')->willReturn($expected);
    $service = ($this->makeService)();

    self::assertSame($expected, $service->findAll());
});

test('find orphans delegates to repository', function () {
    $expected = [($this->makeManagedFile)()];
    $this->managedFileRepository->expects($this->once())->method('findOrphans')->willReturn($expected);
    $service = ($this->makeService)();

    self::assertSame($expected, $service->findOrphans());
});

test('find duplicates delegates to repository', function () {
    $expected = [($this->makeManagedFile)()];
    $this->managedFileRepository->expects($this->once())->method('findDuplicates')->willReturn($expected);
    $service = ($this->makeService)();

    self::assertSame($expected, $service->findDuplicates());
});

test('deduplicate files delegates to the deduplication subservice', function () {
    $this->deduplicationService
        ->expects($this->once())
        ->method('deduplicateFiles')
        ->willReturn(4);
    $service = ($this->makeService)();

    self::assertSame(4, $service->deduplicateFiles());
});

test('generate file hash hashes file contents', function () {
    $filePath = dirname($this->uploadsDirectory).'/uploads/hash-me.txt';
    file_put_contents($filePath, 'hash me');
    $service = ($this->makeService)();

    self::assertSame(hash_file('sha256', $filePath), $service->generateFileHash(filePath: $filePath));
});

test('upload file creates a managed file with a generated hash', function () {
    $sourcePath = dirname($this->uploadsDirectory).'/source-create.txt';
    file_put_contents($sourcePath, 'create content');
    $uploadedFile = ($this->makeUploadedFile)(sourcePath: $sourcePath);
    $uploader = $this->createStub(User::class);
    $persistedFile = null;
    $flushCount = 0;

    $this->entityManager->method('persist')->willReturnCallback(function (ManagedFile $managedFile) use (&$persistedFile): void {
        $persistedFile = $managedFile;
    });
    $this->entityManager->method('flush')->willReturnCallback(function () use (&$flushCount): void {
        ++$flushCount;
    });

    $service = ($this->makeService)();

    $managedFile = $service->uploadFile(
        file: $uploadedFile,
        title: 'Created title',
        description: 'Created description',
        uploader: $uploader,
    );

    self::assertInstanceOf(ManagedFile::class, $managedFile);
    self::assertSame($managedFile, $persistedFile);
    self::assertSame('/uploads', dirname($managedFile->getPath()));
    self::assertStringStartsWith('/uploads/test-file-', $managedFile->getPath());
    self::assertSame('text/plain', $managedFile->getMimeType());
    self::assertSame('Created title', $managedFile->getTitle());
    self::assertSame('Created description', $managedFile->getDescription());
    self::assertSame($uploader, $managedFile->getUploader());
    self::assertSame(
        hash_file('sha256', dirname($this->uploadsDirectory).$managedFile->getPath()),
        $managedFile->getFileHash()
    );
    self::assertSame(1, $flushCount);
});

test('upload file updates an existing managed file and recalculates its hash', function () {
    $sourcePath = dirname($this->uploadsDirectory).'/source-update.txt';
    file_put_contents($sourcePath, 'update content');
    $uploadedFile = ($this->makeUploadedFile)(sourcePath: $sourcePath);
    $existingFile = ($this->makeManagedFile)([
        'path' => '/uploads/original.txt',
        'mimeType' => 'image/png',
        'title' => 'Original title',
        'description' => 'Original description',
        'fileHash' => 'old-hash',
    ]);
    $persistedFile = null;

    $this->entityManager->method('persist')->willReturnCallback(function (ManagedFile $managedFile) use (&$persistedFile): void {
        $persistedFile = $managedFile;
    });
    $this->entityManager->method('flush');

    $service = ($this->makeService)();

    $updatedFile = $service->uploadFile(
        file: $uploadedFile,
        title: 'Updated title',
        description: 'Updated description',
        uploader: $existingFile->getUploader(),
        fileToReplace: $existingFile,
    );

    self::assertSame($existingFile, $updatedFile);
    self::assertSame($existingFile, $persistedFile);
    self::assertStringStartsWith('/uploads/test-file-', $updatedFile->getPath());
    self::assertSame('text/plain', $updatedFile->getMimeType());
    self::assertSame('Updated title', $updatedFile->getTitle());
    self::assertSame('Updated description', $updatedFile->getDescription());
    self::assertSame(
        hash_file('sha256', dirname($this->uploadsDirectory).$updatedFile->getPath()),
        $updatedFile->getFileHash()
    );
});

test('upload file throws when mime type cannot be determined', function () {
    $sourcePath = dirname($this->uploadsDirectory).'/source-missing-mime.txt';
    file_put_contents($sourcePath, 'missing mime');
    $uploadedFile = ($this->makeUploadedFile)(sourcePath: $sourcePath, overrides: ['mimeType' => '']);
    $service = ($this->makeService)();

    $this->expectException(UndeterminedMimeTypeException::class);

    $service->uploadFile(
        file: $uploadedFile,
        title: 'Title',
        description: 'Description',
        uploader: $this->createStub(User::class),
    );
});

test('upload file wraps move failures', function () {
    $uploadedFile = $this->createMock(UploadedFile::class);
    $uploadedFile->method('getMimeType')->willReturn('text/plain');
    $uploadedFile->method('getClientOriginalName')->willReturn('Bad File.txt');
    $uploadedFile->method('guessExtension')->willReturn('txt');
    $uploadedFile
        ->method('move')
        ->willThrowException(new FileException('move failed'));
    $service = ($this->makeService)();

    $this->expectException(GeneralFileManagementException::class);
    $this->expectExceptionMessage('move failed');

    $service->uploadFile(
        file: $uploadedFile,
        title: 'Title',
        description: 'Description',
        uploader: $this->createStub(User::class),
    );
});

test('upload file rejects destinations outside uploads', function () {
    $sourcePath = dirname($this->uploadsDirectory).'/source-invalid-destination.txt';
    file_put_contents($sourcePath, 'invalid destination');
    $uploadedFile = ($this->makeUploadedFile)(sourcePath: $sourcePath);
    $service = ($this->makeService)();

    $this->expectException(MissingUploadPrefixException::class);

    $service->uploadFile(
        file: $uploadedFile,
        title: 'Title',
        description: 'Description',
        uploader: $this->createStub(User::class),
        destination: dirname($this->uploadsDirectory).'/not-uploads',
    );
});
