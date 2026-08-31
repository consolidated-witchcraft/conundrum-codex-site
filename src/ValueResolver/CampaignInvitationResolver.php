<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Services\Campaign\Domain\CampaignInvitation;
use App\Services\Campaign\Service\Interfaces\CampaignServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

readonly class CampaignInvitationResolver implements ValueResolverInterface
{
    public function __construct(private CampaignServiceInterface $campaignService) {}

    /** @return iterable<CampaignInvitation> */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (CampaignInvitation::class !== $argument->getType()) { return []; }
        $code = $request->attributes->get('invitationCode');
        if (!is_string($code)) { return []; }
        $invitation = $this->campaignService->findInvitationByCode(code: $code);
        return null === $invitation ? [] : [$invitation];
    }
}
