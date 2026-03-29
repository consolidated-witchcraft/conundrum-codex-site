<?php

declare(strict_types=1);

use App\Services\PlayEvent\Domain\Enums\PlayEventTypeEnum;
use App\Services\PlayEvent\Domain\PlayEvent;
use App\Services\PlayEvent\Infrastructure\PlayEventRepository;
use App\Services\PlayEvent\Service\PlayEventService;
use App\Services\PuzzleInstance\Domain\Interfaces\PuzzleInstanceInterface;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

beforeEach(function () {
    $this->playEventRepository = $this->createMock(PlayEventRepository::class);
    $this->entityManager = $this->createMock(EntityManagerInterface::class);
    $this->playEventService = new PlayEventService(
        playEventRepository: $this->playEventRepository,
        entityManager: $this->entityManager
    );
});

test('record persists a play event using puzzle instance identifiers', function () {
    $user = $this->createStub(User::class);
    $puzzleInstance = $this->createStub(PuzzleInstanceInterface::class);

    $puzzleInstance
        ->method('getInstanceCode')
        ->willReturn('casebook-alpha');
    $puzzleInstance
        ->method('getTemplateSlug')
        ->willReturn('casebook');

    $this->entityManager
        ->expects($this->once())
        ->method('persist')
        ->with($this->callback(function (mixed $playEvent) use ($user): bool {
            return $playEvent instanceof PlayEvent
                && $playEvent->getUser() === $user
                && $playEvent->getType() === PlayEventTypeEnum::INSTANCE_START
                && $playEvent->getPuzzleInstanceCode() === 'casebook-alpha'
                && $playEvent->getPuzzleTemplateSlug() === 'casebook';
        }));

    $this->entityManager->expects($this->once())->method('flush');

    $playEvent = $this->playEventService->record(
        user: $user,
        puzzleInstance: $puzzleInstance,
        type: PlayEventTypeEnum::INSTANCE_START
    );

    expect($playEvent)->toBeInstanceOf(PlayEvent::class);
});

test('save can skip flushing when requested', function () {
    $playEvent = new PlayEvent(
        user: $this->createStub(User::class),
        type: PlayEventTypeEnum::INSTANCE_WIN,
        puzzleInstanceCode: 'instance-123',
        puzzleTemplateSlug: 'lightforge'
    );

    $this->entityManager->expects($this->once())->method('persist')->with($playEvent);
    $this->entityManager->expects($this->never())->method('flush');

    $result = $this->playEventService->save($playEvent, false);

    expect($result)->toBe($playEvent);
});

test('find delegates to the repository', function () {
    $playEvent = new PlayEvent(
        user: $this->createStub(User::class),
        type: PlayEventTypeEnum::INSTANCE_FAIL,
        puzzleInstanceCode: 'instance-456',
        puzzleTemplateSlug: 'casebook'
    );

    $this->playEventRepository
        ->expects($this->once())
        ->method('find')
        ->with(42)
        ->willReturn($playEvent);

    expect($this->playEventService->find(42))->toBe($playEvent);
});

test('findAll delegates to the repository', function () {
    $playEvents = [
        new PlayEvent(
            user: $this->createStub(User::class),
            type: PlayEventTypeEnum::INSTANCE_START,
            puzzleInstanceCode: 'instance-1',
            puzzleTemplateSlug: 'casebook'
        ),
        new PlayEvent(
            user: $this->createStub(User::class),
            type: PlayEventTypeEnum::INSTANCE_WIN,
            puzzleInstanceCode: 'instance-2',
            puzzleTemplateSlug: 'lightforge'
        ),
    ];

    $this->playEventRepository
        ->expects($this->once())
        ->method('findAll')
        ->willReturn($playEvents);

    expect($this->playEventService->findAll())->toBe($playEvents);
});

test('findByUser delegates to the repository', function () {
    $user = $this->createStub(User::class);
    $playEvents = new ArrayCollection([
        new PlayEvent(
            user: $user,
            type: PlayEventTypeEnum::INSTANCE_START,
            puzzleInstanceCode: 'instance-1',
            puzzleTemplateSlug: 'casebook'
        ),
    ]);

    $this->playEventRepository
        ->expects($this->once())
        ->method('findByUser')
        ->with($user)
        ->willReturn($playEvents);

    expect($this->playEventService->findByUser($user))->toBe($playEvents);
});

test('findAllForUserBetweenDates delegates to the repository', function () {
    $user = $this->createStub(User::class);
    $start = new DateTimeImmutable('2026-03-01 00:00:00');
    $end = new DateTimeImmutable('2026-03-31 23:59:59');
    $playEvents = [
        new PlayEvent(
            user: $user,
            type: PlayEventTypeEnum::INSTANCE_START,
            puzzleInstanceCode: 'instance-3',
            puzzleTemplateSlug: 'casebook'
        ),
    ];

    $this->playEventRepository
        ->expects($this->once())
        ->method('findAllForUserBetweenDates')
        ->with($user, $start, $end)
        ->willReturn($playEvents);

    expect($this->playEventService->findAllForUserBetweenDates($user, $start, $end))->toBe($playEvents);
});
