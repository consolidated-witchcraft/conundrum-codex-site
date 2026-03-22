<?php

declare(strict_types=1);

use App\Services\ManagedFiles\Domain\ManagedFile;
use App\Services\ManagedFiles\Infrastructure\ManagedFileRepository;
use App\Services\ManagedFiles\Service\ManagedFileDeduplicationService;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\ManyToManyOwningSideMapping;
use Doctrine\ORM\Mapping\OneToOneOwningSideMapping;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;

beforeEach(function () {
    $this->entityManager = $this->createMock(EntityManagerInterface::class);
    $this->managedFileRepository = $this->createMock(ManagedFileRepository::class);
    $this->uploadsDirectory = sys_get_temp_dir().'/managed-file-deduplication-tests-'.uniqid('', true).'/uploads';

    if (!is_dir($this->uploadsDirectory)) {
        mkdir($this->uploadsDirectory, 0777, true);
    }

    $this->makeManagedFile = function (array $overrides = []): ManagedFile {
        return new ManagedFile(
            path: $overrides['path'] ?? '/uploads/test.txt',
            mimeType: $overrides['mimeType'] ?? 'application/octet-stream',
            title: $overrides['title'] ?? 'Title',
            description: $overrides['description'] ?? 'Description',
            uploader: $overrides['uploader'] ?? $this->createStub(User::class),
            fileHash: $overrides['fileHash'] ?? '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef',
            id: $overrides['id'] ?? null,
        );
    };

    $this->makeService = function (): ManagedFileDeduplicationService {
        return new ManagedFileDeduplicationService(
            managedFileRepository: $this->managedFileRepository,
            publicFilesDirectory: $this->uploadsDirectory,
            entityManager: $this->entityManager,
        );
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

test('deduplicate files merges duplicates and repoints owning associations', function () {
    $sharedUploader = $this->createStub(User::class);
    $oldestDuplicate = ($this->makeManagedFile)([
        'uploader' => $sharedUploader,
        'path' => '/uploads/duplicate.txt',
        'fileHash' => 'hash-hash-hash-hash-hash-hash-hash-hash-hash-hash-hash-hash-hash1',
        'title' => 'Older title',
        'description' => 'Older description',
        'mimeType' => 'image/jpeg',
        'id' => 1,
    ]);
    $newestDuplicate = ($this->makeManagedFile)([
        'uploader' => $sharedUploader,
        'path' => '/uploads/duplicate.txt',
        'fileHash' => 'hash-hash-hash-hash-hash-hash-hash-hash-hash-hash-hash-hash-hash1',
        'title' => 'Newest title',
        'description' => 'Newest description',
        'mimeType' => 'image/png',
        'id' => 2,
    ]);

    file_put_contents(dirname($this->uploadsDirectory).'/uploads/duplicate.txt', 'deduplicate me');

    $singleReference = new class($oldestDuplicate) {
        public function __construct(
            private ManagedFile $heroImage,
        ) {
        }

        public function getHeroImage(): ManagedFile
        {
            return $this->heroImage;
        }

        public function setHeroImage(ManagedFile $heroImage): void
        {
            $this->heroImage = $heroImage;
        }
    };

    $collectionReference = new class($oldestDuplicate, $newestDuplicate) {
        private ArrayCollection $productVariantImages;

        public function __construct(ManagedFile $first, ManagedFile $second)
        {
            $this->productVariantImages = new ArrayCollection([$first, $second]);
        }

        public function getProductVariantImages(): ArrayCollection
        {
            return $this->productVariantImages;
        }
    };

    $singleAssociationMetadata = $this->createConfiguredMock(ClassMetadata::class, [
        'getName' => $singleReference::class,
        'getAssociationNames' => ['heroImage'],
    ]);
    $singleAssociationMetadata->method('getAssociationMapping')->with('heroImage')->willReturn(
        OneToOneOwningSideMapping::fromMappingArray([
            'fieldName' => 'heroImage',
            'sourceEntity' => $singleReference::class,
            'targetEntity' => ManagedFile::class,
            'isOwningSide' => true,
        ])
    );

    $collectionAssociationMetadata = $this->createConfiguredMock(ClassMetadata::class, [
        'getName' => $collectionReference::class,
        'getAssociationNames' => ['productVariantImages'],
    ]);
    $collectionAssociationMetadata->method('getAssociationMapping')->with('productVariantImages')->willReturn(
        ManyToManyOwningSideMapping::fromMappingArrayAndNamingStrategy(
            [
                'fieldName' => 'productVariantImages',
                'sourceEntity' => $collectionReference::class,
                'targetEntity' => ManagedFile::class,
                'isOwningSide' => true,
            ],
            new UnderscoreNamingStrategy()
        )
    );

    $metadataFactory = $this->createMock(ClassMetadataFactory::class);
    $metadataFactory->method('getAllMetadata')->willReturn([
        $singleAssociationMetadata,
        $collectionAssociationMetadata,
    ]);

    $singleReferenceRepository = $this->createMock(EntityRepository::class);
    $singleReferenceRepository->method('findAll')->willReturn([$singleReference]);

    $collectionReferenceRepository = $this->createMock(EntityRepository::class);
    $collectionReferenceRepository->method('findAll')->willReturn([$collectionReference]);

    $this->managedFileRepository
        ->method('findDuplicateGroups')
        ->willReturn([
            [
                'uploaderId' => 99,
                'path' => '/uploads/duplicate.txt',
                'fileHash' => 'hash-hash-hash-hash-hash-hash-hash-hash-hash-hash-hash-hash-hash1',
                'duplicateCount' => 2,
            ],
        ]);

    $this->managedFileRepository
        ->method('findDuplicateGroup')
        ->with(
            uploaderId: 99,
            path: '/uploads/duplicate.txt',
            fileHash: 'hash-hash-hash-hash-hash-hash-hash-hash-hash-hash-hash-hash-hash1',
        )
        ->willReturn([$newestDuplicate, $oldestDuplicate]);

    $this->entityManager->method('getMetadataFactory')->willReturn($metadataFactory);
    $this->entityManager->method('getRepository')->willReturnCallback(
        function (string $className) use ($singleReference, $collectionReference, $singleReferenceRepository, $collectionReferenceRepository) {
            return match ($className) {
                $singleReference::class => $singleReferenceRepository,
                $collectionReference::class => $collectionReferenceRepository,
                default => throw new RuntimeException(sprintf('Unexpected repository request for %s', $className)),
            };
        }
    );

    $persistedEntities = [];
    $removedEntities = [];
    $flushCount = 0;

    $this->entityManager->method('persist')->willReturnCallback(function (object $entity) use (&$persistedEntities) {
        $persistedEntities[] = $entity;
    });
    $this->entityManager->method('remove')->willReturnCallback(function (object $entity) use (&$removedEntities) {
        $removedEntities[] = $entity;
    });
    $this->entityManager->method('flush')->willReturnCallback(function () use (&$flushCount) {
        ++$flushCount;
    });

    $service = ($this->makeService)();

    $deletedFilesCount = $service->deduplicateFiles();

    self::assertSame(2, $deletedFilesCount);
    self::assertSame(ManagedFile::class, $singleReference->getHeroImage()::class);
    self::assertSame($singleReference->getHeroImage(), $collectionReference->getProductVariantImages()->first());
    self::assertCount(1, $collectionReference->getProductVariantImages());
    self::assertSame('Newest title', $singleReference->getHeroImage()->getTitle());
    self::assertSame('Newest description', $singleReference->getHeroImage()->getDescription());
    self::assertSame('text/plain', $singleReference->getHeroImage()->getMimeType());
    self::assertSame($sharedUploader, $singleReference->getHeroImage()->getUploader());
    self::assertSame($newestDuplicate->getFileHash(), $singleReference->getHeroImage()->getFileHash());
    self::assertContains($newestDuplicate, $removedEntities);
    self::assertContains($oldestDuplicate, $removedEntities);
    self::assertGreaterThanOrEqual(3, count($persistedEntities));
    self::assertSame(1, $flushCount);
});

test('deduplicate files skips duplicate groups when the filesystem file is missing', function () {
    $duplicate = ($this->makeManagedFile)([
        'path' => '/uploads/missing.txt',
        'fileHash' => 'missing-hash',
        'id' => 11,
    ]);

    $this->managedFileRepository
        ->method('findDuplicateGroups')
        ->willReturn([
            [
                'uploaderId' => 5,
                'path' => '/uploads/missing.txt',
                'fileHash' => 'missing-hash',
                'duplicateCount' => 2,
            ],
        ]);

    $this->managedFileRepository
        ->method('findDuplicateGroup')
        ->willReturn([$duplicate, ($this->makeManagedFile)([
            'path' => '/uploads/missing.txt',
            'fileHash' => 'missing-hash',
            'id' => 12,
        ])]);

    $this->entityManager->expects($this->never())->method('persist');
    $this->entityManager->expects($this->never())->method('remove');
    $this->entityManager->expects($this->never())->method('flush');

    $service = ($this->makeService)();

    self::assertSame(0, $service->deduplicateFiles());
});
