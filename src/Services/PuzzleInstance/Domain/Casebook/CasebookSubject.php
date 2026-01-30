<?php

namespace App\Services\PuzzleInstance\Domain\Casebook;

use ApiPlatform\Metadata\ApiResource;
use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Files\Domain\ManagedFile;
use App\Services\PuzzleInstance\Infrastructure\Casebook\Repository\CasebookSubjectRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CasebookSubjectRepository::class)]
class CasebookSubject extends AbstractDomainEntity
{

    public function __construct(
        #[ORM\Column(length: 255)]
        private string $name,

        #[ORM\Column(length: 2048)]
        private string $description,

        #[ORM\ManyToOne(inversedBy: 'casebookSubjects')]
        #[ORM\JoinColumn(nullable: false)]
        private Casebook $casebook,

        #[ORM\ManyToOne(targetEntity: CasebookSubjectType::class)]
        #[ORM\JoinColumn(nullable: false)]
        private CasebookSubjectType $casebookSubjectType,

        /**
         * @var Collection<int, CasebookSubjectClue>
         */
        #[ORM\OneToMany(targetEntity: CasebookSubjectClue::class, mappedBy: 'casebookSubject', cascade: ['persist'], orphanRemoval: true)]
        private Collection $casebookSubjectClues,

        /**
         * @var Collection<int, CasebookSubjectNote>
         */
        #[ORM\OneToMany(targetEntity: CasebookSubjectNote::class, mappedBy: 'casebookSubject', cascade: ['persist'], orphanRemoval: true)]
        private Collection $casebookSubjectNotes,

        #[ORM\Column(type: 'datetime_immutable', nullable: true)]
        private ? \DateTimeImmutable $revealedDate = null,

        #[ORM\OneToOne(targetEntity: ManagedFile::class)]
        private ? ManagedFile $casebookSubjectImageFile = null,

        ?int $id = null
    ) {
        parent::__construct($id);
    }

    public function getCasebookSubjectImageFile(): ?ManagedFile
    {
        return $this->casebookSubjectImageFile;
    }

    public function setCasebookSubjectImageFile(ManagedFile $casebookSubjectImageFile): void
    {
        $this->casebookSubjectImageFile = $casebookSubjectImageFile;
    }

    public function getRevealedDate(): ?\DateTimeImmutable
    {
        return $this->revealedDate;
    }

    public function getCasebookSubjectType(): CasebookSubjectType
    {
        return $this->casebookSubjectType;
    }

    public function setCasebookSubjectType(CasebookSubjectType $casebookSubjectType): void
    {
        $this->casebookSubjectType = $casebookSubjectType;
    }

    /**
     * @return Collection<int, CasebookSubjectNote>
     */
    public function getCasebookSubjectNotes(): Collection
    {
        return $this->casebookSubjectNotes;
    }

    /**
     * @param Collection<int, CasebookSubjectNote> $casebookSubjectNotes
     * @return void
     */
    public function setCasebookSubjectNotes(Collection $casebookSubjectNotes): void
    {
        $this->casebookSubjectNotes = $casebookSubjectNotes;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }


    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCasebook(): Casebook
    {
        return $this->casebook;
    }

    public function setCasebook(Casebook $casebook): void
    {
        $this->casebook = $casebook;
    }

    /**
     * @return Collection<int, CasebookSubjectClue>
     */
    public function getCasebookSubjectClues(): Collection
    {
        return $this->casebookSubjectClues;
    }

    /**
     * @return Collection<int, CasebookSubjectClue>
     */
    public function getRevealedCasebookSubjectClues(): Collection
    {
        return $this->getCasebookSubjectClues()->filter(function (CasebookSubjectClue $clue) {
            return $clue->getRevealedDate() !== null;
        });
    }

    public function isRevealed(): bool {
        return $this->revealedDate !== null;
    }

    public function markRevealed(): void {
        $this->revealedDate = new \DateTimeImmutable();
    }

    public function unreveal(): void {
        $this->revealedDate = null;
    }

}
