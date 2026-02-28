<?php

namespace App\Services\PuzzleInstance\Domain\Casebook;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\PuzzleInstance\Domain\Casebook\Interfaces\CasebookSubjectClueInterface;
use App\Services\PuzzleInstance\Infrastructure\Casebook\Repository\CasebookSubjectClueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CasebookSubjectClueRepository::class)]
/**
 * @extends AbstractDomainEntity
 */
class CasebookSubjectClue
    extends AbstractDomainEntity
    implements CasebookSubjectClueInterface
{
    public function __construct(
        #[ORM\Column(type: 'non_empty_string', length: 255)]
        private string                             $title,

        #[ORM\Column(type: 'non_empty_string', length: 1024)]
        private string                             $body,

        #[ORM\ManyToOne(targetEntity: CasebookSubjectClueTypeValueObject::class)]
        #[ORM\JoinColumn(nullable: false)]
        private CasebookSubjectClueTypeValueObject $type,

        #[ORM\ManyToOne(inversedBy: 'casebookSubjectClues')]
        #[ORM\JoinColumn(nullable: false)]
        private CasebookSubject                    $casebookSubject,

        #[ORM\Column(type: 'datetime_immutable', nullable: true)]
        private ? \DateTimeImmutable               $revealedDate = null,
        ?int                                       $id = null
    ) {
        parent::__construct($id);
    }

    public function setType(CasebookSubjectClueTypeValueObject $type): void {
        $this->type = $type;
    }

    public function getType(): CasebookSubjectClueTypeValueObject
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param non-empty-string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param non-empty-string $body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function setCasebookSubject(CasebookSubject $casebookSubject): void {
        $this->casebookSubject = $casebookSubject;
    }
    public function getCasebookSubject(): CasebookSubject
    {
        return $this->casebookSubject;
    }

    public function getRevealedDate(): ?\DateTimeInterface
    {
        return $this->revealedDate;
    }

    public function reveal(): void {
        $this->revealedDate = new \DateTimeImmutable();
    }
}
