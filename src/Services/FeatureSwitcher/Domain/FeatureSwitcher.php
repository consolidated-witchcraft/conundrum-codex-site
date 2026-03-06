<?php

declare(strict_types=1);

namespace App\Services\FeatureSwitcher\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\FeatureSwitcher\Infrastructure\FeatureSwitcherRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: FeatureSwitcherRepository::class)]
#[UniqueEntity(fields: ['handle'])]
class FeatureSwitcher extends AbstractDomainEntity
{
  public function __construct(
      #[ORM\Column(type: 'string', length: 255, unique: true)]
      private readonly string $handle,

      #[ORM\Column(type: 'string', length: 255)]
      private readonly string $label,

      #[ORM\Column(type: 'boolean', length: 255)]
      private bool $enabled = false,
      ? int $id = null
  ) {
      parent::__construct($id);
  }

    public function getHandle(): string
    {
        return $this->handle;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function enable(): void {
        $this->enabled = true;
    }

    public function disable(): void {
        $this->enabled = false;
    }

}
