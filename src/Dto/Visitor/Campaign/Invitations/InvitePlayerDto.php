<?php

declare(strict_types=1);

namespace App\Dto\Visitor\Campaign\Invitations;

use App\Validator as CustomAssert;
use Symfony\Component\Validator\Constraints as Assert;

class InvitePlayerDto
{
    #[Assert\Email]
    #[Assert\NotBlank]
    #[CustomAssert\EmailAddressIsNotBlockedConstraint]
    public ?string $email = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public ?string $invitationMessage = null;
}
