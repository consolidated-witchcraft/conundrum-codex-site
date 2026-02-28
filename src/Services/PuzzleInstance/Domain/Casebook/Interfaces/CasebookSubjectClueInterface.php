<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Domain\Casebook\Interfaces;

use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubject;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectClueTypeValueObject;

interface CasebookSubjectClueInterface
{

    /**
     * @param non-empty-string $title
     * @param non-empty-string $body
     * @param CasebookSubjectClueTypeValueObject $type
     * @param CasebookSubject $casebookSubject
     * @param \DateTimeImmutable|null $revealedDate
     * @param int|null $id
     */
    public function __construct(
        string                             $title,
        string                             $body,
        CasebookSubjectClueTypeValueObject $type,
        CasebookSubject                    $casebookSubject,
        ? \DateTimeImmutable               $revealedDate = null,
        ?int                               $id = null
    );

    public function setType(CasebookSubjectClueTypeValueObject $type): void;

    public function getType(): CasebookSubjectClueTypeValueObject;

    /**
     * @return non-empty-string
     */
    public function getTitle(): string;

    /**
     * @param non-empty-string $title
     */
    public function setTitle(string $title): void;

    /**
     * @return non-empty-string
     */
    public function getBody(): string;

    /**
     * @param non-empty-string $body
     */
    public function setBody(string $body): void;

    public function setCasebookSubject(CasebookSubject $casebookSubject): void ;
    public function getCasebookSubject(): CasebookSubject;

    public function getRevealedDate(): ?\DateTimeInterface;

    public function reveal(): void;
}
