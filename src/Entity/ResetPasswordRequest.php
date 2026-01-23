<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ResetPasswordRequestRepository;
use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\User\Domain\User;
use Doctrine\ORM\Mapping as ORM;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestTrait;

#[ORM\Entity(repositoryClass: ResetPasswordRequestRepository::class)]
class ResetPasswordRequest extends AbstractDomainEntity implements ResetPasswordRequestInterface
{
    use ResetPasswordRequestTrait;

    public function __construct(
        #[ORM\JoinColumn(nullable: false)]
        #[ORM\ManyToOne(targetEntity: User::class)]
        private ? User $user,

        \DateTimeInterface $expiresAt,
        string $selector,
        string $hashedToken,
        ?int $id = null,
    ) {
        parent::__construct($id);
        $this->initialize($expiresAt, $selector, $hashedToken);
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
