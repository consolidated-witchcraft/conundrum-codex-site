<?php

namespace App\Services\PuzzleInstance\Domain\Dynamic;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Game\Domain\Game;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\DynamicPuzzleFieldInterface;
use App\Services\PuzzleInstance\Domain\Interfaces\DynamicPuzzleInstanceInterface;
use App\Services\PuzzleInstance\Infrastructure\PuzzleInstanceRepository;
use App\Services\PuzzleTemplate\Domain\Exceptions\PuzzleTemplateNotMappedException;
use App\Services\PuzzleTemplate\Domain\PuzzleTemplate;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PuzzleInstanceRepository::class)]
class PuzzleInstance extends AbstractDomainEntity implements DynamicPuzzleInstanceInterface
{
    private ? PuzzleTemplate $puzzleTemplate = null;


    /**
     * @param non-empty-string $instanceCode
     * @param non-empty-string $name
     * @param non-empty-string $description
     * @param Game $game
     * @param non-empty-string $templateSlug
     * @param \DateTimeInterface|null $publicationDate
     * @param DynamicPuzzleFieldInterface[] $config
     * @param int|null $id
     */
    public function __construct(
        #[ORM\Column(type: 'non_empty_string', length: 255, unique: true)]
        private string $instanceCode,

        #[ORM\Column(type: 'non_empty_string', length: 255)]
        private string $name,

        #[ORM\Column(type: 'non_empty_string', length: 2048)]
        private string $description,

        #[ORM\ManyToOne(inversedBy: 'dynamicPuzzleInstances')]
        #[ORM\JoinColumn(nullable: false)]
        private Game $game,

        #[ORM\Column(type: 'non_empty_string', length:1024)]
        private readonly string $templateSlug,

        #[ORM\Column(type: 'datetime', nullable: true)]
        private ?\DateTimeInterface $publicationDate = null,

        #[ORM\Column(type: 'puzzle_config_fields', nullable: false)]
        private array $config = [],

        ?int $id = null
    ){
        parent::__construct($id);
    }

    /**
     * @return non-empty-string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param non-empty-string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return non-empty-string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param non-empty-string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return non-empty-string
     */
    public function getInstanceCode(): string
    {
        return $this->instanceCode;
    }

    /**
     * @param non-empty-string $instanceCode
     */
    public function setInstanceCode(string $instanceCode): void
    {
        $this->instanceCode = $instanceCode;
    }

    public function getGame(): Game
    {
        return $this->game;
    }

    public function setGame(Game $game): void
    {
        $this->game = $game;
    }

    /**
     * @return DynamicPuzzleFieldInterface[]
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    public function getConfigValue(string $configOptionName): mixed {
        foreach ($this->config as $configOption) {
            if ($configOption->getName() === $configOptionName) {
                return $configOption->getValue();
            }
        }
        return null;
    }

    /**
     * @param DynamicPuzzleFieldInterface[] $config
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(\DateTimeInterface $publicationDate): void
    {
        $this->publicationDate = $publicationDate;
    }

    public function getTemplateSlug(): string
    {
        return $this->templateSlug;
    }

    public function isPublished(): bool
    {
        $date= new \DateTime();
        return $this->publicationDate !== null && $date >= $this->publicationDate;
    }

    /**
     * @throws PuzzleTemplateNotMappedException
     */
    public function getTemplate(): PuzzleTemplate
    {
        if ($this->puzzleTemplate === null) {
            throw new PuzzleTemplateNotMappedException();
        }
        return $this->puzzleTemplate;
    }

    public function setTemplate(PuzzleTemplate $puzzleTemplate): void
    {
        $this->puzzleTemplate = $puzzleTemplate;
    }
}
