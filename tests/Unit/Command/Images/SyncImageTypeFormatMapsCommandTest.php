<?php

declare(strict_types=1);

use App\Command\Images\SyncImageTypeFormatMapsCommand;
use App\Services\ManagedImages\Service\ImageTypeFormatMapSynchronizer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

test('command syncs image type format maps and reports summary', function () {
    $synchronizer = $this->createMock(ImageTypeFormatMapSynchronizer::class);
    $synchronizer
        ->expects($this->once())
        ->method('sync')
        ->willReturn(2);

    $command = new SyncImageTypeFormatMapsCommand($synchronizer);
    $commandTester = new CommandTester($command);

    $exitCode = $commandTester->execute([]);
    $display = $commandTester->getDisplay();

    self::assertSame(Command::SUCCESS, $exitCode);
    self::assertStringContainsString('Updated 2 maps', $display);
});
