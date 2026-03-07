<?php

declare(strict_types=1);

namespace App\Services\FeatureSwitcher\Service\Exceptions;

class UnknownFeatureHandleException extends \Exception
{
    private const string MESSAGE_PATTERN = "No feature with handle '%s' exists";

    public function __construct(string $featureHandle, int $code = 0, ?\Throwable $previous = null) {
        parent::__construct(sprintf(self::MESSAGE_PATTERN, $featureHandle), $code, $previous);
    }
}
