<?php

declare(strict_types=1);

namespace App\Services\Core\Domain;

use App\Services\Core\Domain\Interfaces\DomainEntityInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class AbstractDomainEntity implements DomainEntityInterface
{
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column]
        protected ?int $id = null
    ) {
        if (!isset($this->createdAt)) {
            $this->createdAt = new \DateTimeImmutable();
        }

        if (!isset($this->updatedAt)) {
            $this->updatedAt = new \DateTimeImmutable();
        }

    }

    final public function getId(): ?int {
        return $this->id ?? null;
    }

    final public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getCreatedAt(): \DateTimeImmutable {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    public function prePersist(): void {
        $persistTime = new \DateTimeImmutable();
        $this->createdAt = $persistTime;
        $this->updatedAt = $persistTime;

    }

    #[ORM\PreUpdate]
    public function preUpdate(): void {
        $this->updatedAt = new \DateTimeImmutable();
    }

}
