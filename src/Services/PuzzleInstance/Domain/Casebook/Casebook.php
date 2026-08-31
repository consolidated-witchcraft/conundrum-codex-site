<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Domain\Casebook;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Game\Domain\Game;
use App\Services\PuzzleTemplate\Domain\Exceptions\MismappedPuzzleTemplateException;
use App\Services\PuzzleTemplate\Domain\Exceptions\PuzzleTemplateNotMappedException;
use App\Services\PuzzleInstance\Domain\Interfaces\StaticPuzzleInstanceInterface;
use App\Services\PuzzleTemplate\Domain\PuzzleTemplate;
use App\Services\PuzzleInstance\Infrastructure\Casebook\Repository\CasebookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CasebookRepository::class)]
#[UniqueEntity(fields: ['slug'], message: 'There is already a casebook with this slug')]
class Casebook extends AbstractDomainEntity implements StaticPuzzleInstanceInterface
{
    public const string TEMPLATE_SLUG = 'casebook';

    private ? PuzzleTemplate $puzzleTemplate = null;

    /**
     * @var Collection<int, CasebookSubject>
     */
    #[ORM\OneToMany(targetEntity: CasebookSubject::class, mappedBy: 'casebook', orphanRemoval: true)]
    private Collection $casebookSubjects;

    public function __construct(
        #[ORM\Column(length: 255)]
        private string $name,

        #[ORM\Column(length: 255, unique: true)]
        private string $slug,

        #[ORM\ManyToOne(targetEntity: Game::class)]
        #[ORM\JoinColumn(nullable: false)]
        private Game $game,

        #[ORM\Column(length: 2048)]
        private string $brief,

        #[ORM\Column(type: 'datetime', nullable: true)]
        private ?\DateTimeInterface $publicationDate = null,

        ? int $id = null
    ) {
        parent::__construct($id);
        $this->casebookSubjects = new ArrayCollection();
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getBrief(): string
    {
        return $this->brief;
    }

    public function setBrief(string $brief): void
    {
        $this->brief = $brief;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Collection<int, CasebookSubject>
     */
    public function getCasebookSubjects(): Collection
    {
        return $this->casebookSubjects;
    }

    public function addCasebookSubject(CasebookSubject $casebookSubject): static
    {
        if (!$this->casebookSubjects->contains($casebookSubject)) {
            $this->casebookSubjects->add($casebookSubject);
            $casebookSubject->setCasebook($this);
        }

        return $this;
    }

    public function getGame(): Game {
        return $this->game;
    }

    public function getInstanceCode(): string
    {
        return $this->slug;
    }

    public function setInstanceCode(string $instanceCode): void
    {
        $this->slug = $instanceCode;
    }

    public function getDescription(): string
    {
        return $this->brief;
    }

    public function setGame(Game $game): void
    {
        $this->game = $game;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(\DateTimeInterface $publicationDate): void
    {
        $this->publicationDate = $publicationDate;
    }


    /**
     * @return string
     */
    public function getTemplateSlug(): string
    {
        return self::TEMPLATE_SLUG;
    }

    public function isPublished(): bool
    {
        $date= new \DateTime();
        return $this->publicationDate !== null && $this->publicationDate <= $date;
    }

    /**
     * @throws PuzzleTemplateNotMappedException
     * @throws MismappedPuzzleTemplateException
     */
    public function getTemplate(): PuzzleTemplate
    {
        if (null === $this->puzzleTemplate) {
            throw new PuzzleTemplateNotMappedException();
        }
        if (self::TEMPLATE_SLUG !== $this->puzzleTemplate->getSlug()) {
            throw new MismappedPuzzleTemplateException();
        }
        return $this->puzzleTemplate;
    }

    /**
     * @throws MismappedPuzzleTemplateException
     */
    public function setTemplate(PuzzleTemplate $puzzleTemplate): void
    {
        if (self::TEMPLATE_SLUG !== $puzzleTemplate->getSlug()) {
            throw new MismappedPuzzleTemplateException();
        }
        $this->puzzleTemplate = $puzzleTemplate;
    }

    /**
     * @param string $typeHandle
     * @return Collection<int, CasebookSubject>
     */
    public function getSubjectsByTypeHandle(string $typeHandle): Collection
    {

        $criteria = (new Criteria(accessRawFieldValues: true))
            ->orderBy(["name" => Order::Ascending]);


        $filteredSubjects = $this->casebookSubjects->filter(function (CasebookSubject $casebookSubject) use ($typeHandle) {
            return $casebookSubject->getCasebookSubjectType()->getHandle() === $typeHandle;
        });

        return $filteredSubjects->matching($criteria);

    }

    /**
     * @param string $typeHandle
     * @param string $initial
     * @return Collection<int, CasebookSubject>
     */
    public function getSubjectsByTypeHandleAndInitial(string $typeHandle, string $initial): Collection {
        $typeHandle = strtolower($typeHandle);
        $filteredSubjects = $this->getSubjectsByTypeHandle($typeHandle);

        return $filteredSubjects->filter(function (CasebookSubject $casebookSubject) use ($initial) {
            $nameToCheck = $casebookSubject->getName();
            if (str_starts_with($nameToCheck, "The ")) {
                $nameToCheck = substr($nameToCheck, strlen("The "));
            }
           return substr(strtolower($nameToCheck), 0, 1) === $initial;
        });
    }
}
