<?php

namespace App\ValueResolver;


use App\Services\Files\Domain\ManagedFile;
use App\Services\Files\Service\Interfaces\FilesServiceInterface;
use App\Services\Game\Domain\GameInvitation;
use App\Services\Game\Service\Interfaces\GameServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

readonly class ManagedFileResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly FilesServiceInterface $filesService,
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        // get the argument type (e.g. BookingId)
        $argumentType = $argument->getType();
        if ($argumentType !== ManagedFile::class) {
            return [];
        }

        $fileId = $request->attributes->get('fileId');

        if (!is_string($fileId) && !is_numeric($fileId)) {
            return [];
        }

        $file = $this->filesService->find((int) $fileId);
        // create and return the value object
        return $file ? [$file] : [];
    }
}
