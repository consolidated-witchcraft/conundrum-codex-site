<?php

declare(strict_types=1);

use App\Command\Images\BackfillAvatarManagedImageTypesCommand;
use App\Services\ManagedFiles\Domain\ManagedFile;
use App\Services\ManagedImages\Domain\Enums\ManagedImageTypeEnum;
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\ManagedImages\Infrastructure\ManagedImageRepository;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

beforeEach(function () {
    $this->managedImageRepository = $this->createMock(ManagedImageRepository::class);
    $this->entityManager = $this->createMock(EntityManagerInterface::class);

    $this->makeManagedFile = function (array $overrides = []): ManagedFile {
        return new ManagedFile(
            path: $overrides['path'] ?? '/uploads/test-image.webp',
            fileHash: $overrides['fileHash'] ?? '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef',
            mimeType: $overrides['mimeType'] ?? 'image/webp',
            title: $overrides['title'] ?? 'Test image',
            description: $overrides['description'] ?? 'Test description',
            uploader: $overrides['uploader'] ?? $this->createStub(User::class),
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
            id: $overrides['id'] ?? 42,
        );
    };
});

test('command backfills default avatar descriptions to avatar type', function () {
    $avatarManagedImage = ($this->makeManagedImage)([
        'description' => 'Alice avatar',
        'type' => ManagedImageTypeEnum::DEFAULT,
        'id' => 10,
    ]);
    $secondAvatarManagedImage = ($this->makeManagedImage)([
        'description' => 'Bob avatar',
        'type' => ManagedImageTypeEnum::DEFAULT,
        'id' => 11,
    ]);

    $this->managedImageRepository
        ->expects($this->once())
        ->method('findAllByDescriptionSuffixAndType')
        ->with(' avatar', ManagedImageTypeEnum::DEFAULT)
        ->willReturn([$avatarManagedImage, $secondAvatarManagedImage]);

    $this->entityManager->expects($this->once())->method('flush');

    $command = new BackfillAvatarManagedImageTypesCommand(
        managedImageRepository: $this->managedImageRepository,
        entityManager: $this->entityManager,
    );

    $commandTester = new CommandTester($command);
    $exitCode = $commandTester->execute([]);
    $display = $commandTester->getDisplay();

    self::assertSame(Command::SUCCESS, $exitCode);
    self::assertSame(ManagedImageTypeEnum::AVATAR, $avatarManagedImage->getType());
    self::assertSame(ManagedImageTypeEnum::AVATAR, $secondAvatarManagedImage->getType());
    self::assertStringContainsString('Updated 2 images', $display);
});
