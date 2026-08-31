<?php

declare(strict_types=1);

namespace App\Services\User\Domain\ValueObjects;

use App\Services\Core\Domain\AbstractValueObject;
use App\Services\User\Infrastructure\Repository\UserAccountTypeRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: UserAccountTypeRepository::class)]
#[UniqueEntity(fields: ['handle'], message: 'There is already an account type with this handle. Please choose another one.')]
class UserAccountType extends AbstractValueObject
{
    public function __construct(
        string $label,
        string $handle,
        #[ORM\Column(length: 255)]
        private string $description,
        ?int $id = null,
    ) {
        parent::__construct(
            label: $label,
            handle: $handle,
            id: $id
        );
    }
    public static function hasDescription(): bool
    {
        return true;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

}
