<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Services\Character\Domain\Character;
use App\Services\Character\Service\Interfaces\CharacterServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

readonly class CharacterResolver implements ValueResolverInterface
{
    public function __construct(
        private CharacterServiceInterface $characterService,
    ) {}

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== Character::class) {
            return [];
        }

        $id = $request->attributes->get('characterId');
        if (!is_numeric($id)) {
            return [];
        }

        $character = $this->characterService->find((int) $id);

        return $character instanceof Character ? [$character] : [];
    }
}
