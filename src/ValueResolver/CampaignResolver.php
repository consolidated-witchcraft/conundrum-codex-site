<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Services\Campaign\Domain\Campaign;
use App\Services\Campaign\Service\Interfaces\CampaignServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

readonly class CampaignResolver implements ValueResolverInterface
{
    public function __construct(private CampaignServiceInterface $campaignService) {}

    /** @return iterable<Campaign> */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (Campaign::class !== $argument->getType()) { return []; }
        $slug = $request->attributes->get('campaignSlug');
        if (!is_string($slug)) { return []; }
        $campaign = $this->campaignService->findBySlug(slug: $slug);
        return null === $campaign ? [] : [$campaign];
    }
}
