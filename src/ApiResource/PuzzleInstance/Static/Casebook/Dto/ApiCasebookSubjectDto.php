<?php

declare(strict_types=1);

namespace App\ApiResource\PuzzleInstance\Static\Casebook\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use App\ApiResource\PuzzleInstance\ApiPuzzleInstanceDtoInterface;
use App\ApiResource\PuzzleInstance\Static\Casebook\Providers\CasebookSubjectProvider;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubject;
use Doctrine\Common\Collections\ArrayCollection;

#[ApiResource(
    uriTemplate: '/puzzles/instances/static/casebook/{instanceCode}/subjects/{subject}',
    operations: [
        new Get()
    ],
    uriVariables: [
        'instanceCode' => new Link(
            fromProperty: 'instanceCode',
            fromClass: ApiCasebookDtoApi::class
        ),

        'subject' => new Link(
            fromProperty: 'id',
            fromClass: ApiCasebookSubjectDto::class
        ),
    ],
    stateless: false,
    security: 'is_granted("ROLE_USER")',
    provider: CasebookSubjectProvider::class
)]
class ApiCasebookSubjectDto implements ApiPuzzleInstanceDtoInterface
{

    public function getSubjectId(): ?int {
        return $this->id;
    }

    /**
     * @var ArrayCollection<int, ApiCasebookSubjectClueDto>
     */
    public ArrayCollection $clues;

    final public function __construct(
        public string $instanceCode,
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
            instanceCode: $subject->getCasebook()->getInstanceCode(),
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

    public function getInstanceCode(): string
    {
        return $this->instanceCode;
    }

}
