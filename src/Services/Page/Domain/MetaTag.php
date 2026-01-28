<?php

declare(strict_types=1);

namespace App\Services\Page\Domain;

readonly class MetaTag
{
    public function __construct(
        public string $name,
        public string $content
    ) {}
}
