<?php

declare(strict_types=1);

namespace App\ApiResource\Casebook\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use App\ApiResource\Casebook\Providers\CasebookSubjectProvider;
use App\Services\Puzzle\Domain\Casebook\CasebookSubject;
use Doctrine\Common\Collections\ArrayCollection;

#[ApiResource(
    uriTemplate: '/puzzles/static/casebook/{instanceCode}/subjects/{subject}',
    operations: [
        new Get()
    ],
    uriVariables: [
        'instanceCode' => new Link(
            fromProperty: 'instanceCode',
            fromClass: ApiCasebookDto::class
        ),

        'subject' => new Link(
            fromProperty: 'id',
            fromClass: ApiCasebookSubjectDto::class
        ),
    ],
    stateless: false,
    provider: CasebookSubjectProvider::class
)]
class ApiCasebookSubjectDto
{

    public function getSubjectId(): ?int {
        return $this->id;
    }

    /**
     * @var ArrayCollection<int, ApiCasebookSubjectClueDto>
     */
    public ArrayCollection $clues;

    final public function __construct(
        public string $name,
        public string $description,
        public string $type,
        public bool $isRevealed = false,
        public ? string $imageUri = null,
        public ? int $id = null
    ) {
        $this->clues = new ArrayCollection();
    }

    public function getId(): ? int {
        return $this->id;
    }
    public static function makeFromSubject(CasebookSubject $subject): static {

        $dto = new static (
            name: $subject->getName(),
            description: $subject->getDescription(),
            type: $subject->getCasebookSubjectType()->getHandle(),
            isRevealed: $subject->isRevealed(),
            imageUri: $subject->getCasebookSubjectImageFile()->getPath(),
            id: $subject->getId()
        );
        foreach ($subject->getRevealedCasebookSubjectClues() as $revealedClue) {
            $dto->clues->add(ApiCasebookSubjectClueDto::makeFromCasebookSubjectClue($revealedClue));
        }
        return $dto;
    }
}
