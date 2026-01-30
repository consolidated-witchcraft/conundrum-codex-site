<?php

declare(strict_types=1);

namespace App\ApiResource\PuzzleTemplate\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use App\ApiResource\PuzzleTemplate\Provider\PuzzleTemplateDtoProvider;
use App\Services\Puzzle\Domain\PuzzleTemplate;

#[ApiResource(
    uriTemplate: '/puzzles/templates',
    operations: [
        new Get(
            uriTemplate: '/puzzles/templates/{templateSlug}',
            uriVariables: [
                'templateSlug' => new Link(
                    fromProperty: 'templateSlug',
                    fromClass: PuzzleTemplateDto::class
                )
            ],
        ),
        new GetCollection(),
    ],

    stateless: false,
    provider: PuzzleTemplateDtoProvider::class
)]
readonly class PuzzleTemplateDto
{
    final public function __construct(
        public string $templateSlug,
        public string $title,
        public \DateTimeInterface $createdAt,
        public string $description,
        public array $categories,
        public array $credits
    ) {}

    public static function makeFromTemplate(PuzzleTemplate $template): static {
        return new static(
            templateSlug: $template->getSlug(),
            title: $template->getTitle(),
            createdAt: $template->getCreatedAt(),
            description: $template->getDescription(),
            categories: $template->getCategories()->toArray(),
            credits: $template->getCredits()
        );
    }
}
