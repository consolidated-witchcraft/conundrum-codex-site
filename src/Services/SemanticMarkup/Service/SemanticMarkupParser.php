<?php

declare(strict_types=1);

namespace App\Services\SemanticMarkup\Service;

use App\Services\SemanticMarkup\Domain\Nodes\FactNode;
use App\Services\SemanticMarkup\Domain\Nodes\Interfaces\SemanticNodeInterface;
use App\Services\SemanticMarkup\Domain\Nodes\TextNode;
use App\Services\SemanticMarkup\Domain\Nodes\WorldEntityReferenceNode;
use App\Services\SemanticMarkup\Domain\ValueObjects\SourceSpan;

final readonly class SemanticMarkupParser
{
    private const string TOKEN_PATTERN = '/\[(?<tag>[^\]]+)\]\((?<label>[^)]+)\)/u';

    public static function isStringBlank(string $string): bool
    {
        return trim($string) === "";
    }

    public function parse(string $source): ParseResult
    {
        preg_match_all(
            self::TOKEN_PATTERN,
            $source,
            $matches,
            PREG_OFFSET_CAPTURE
        );

        $nodes = [];
        $errors = [];
        $cursor = 0;

        foreach ($matches[0] as $i => [$raw, $offset]) {
            $offset = (int) $offset;

            $rawLength = strlen($raw);
            $tokenEnd = $offset + $rawLength;

            if ($offset > $cursor) {
                $text = substr($source, $cursor, $offset - $cursor);

                if (!self::isStringBlank($text)) {
                    $nodes[] = new TextNode(
                        raw: $text,
                        sourceSpan: new SourceSpan(
                            start: $cursor,
                            end: $offset,
                        ),
                        text: $text,
                    );
                }
            }

            $tag = trim($matches['tag'][$i][0]);
            $label = trim($matches['label'][$i][0]);

            $sourceSpan = new SourceSpan(
                start: $offset,
                end: $tokenEnd,
            );

            $parsed = $this->parseToken(
                raw: $raw,
                span: $sourceSpan,
                tag: $tag,
                label: $label,
            );

            if ($parsed instanceof ParseError) {
                $errors[] = $parsed;

                $nodes[] = new TextNode(
                    raw: $raw,
                    sourceSpan: $sourceSpan,
                    text: $raw,
                );
            } else {
                $nodes[] = $parsed;
            }

            $cursor = $tokenEnd;
        }

        $sourceLength = strlen($source);

        if ($cursor < $sourceLength) {
            $text = substr($source, $cursor);

            if (!self::isStringBlank($text)) {
                $nodes[] = new TextNode(
                    raw: $text,
                    sourceSpan: new SourceSpan(
                        start: $cursor,
                        end: $sourceLength,
                    ),
                    text: $text,
                );
            }
        }

        return new ParseResult($nodes, $errors);
    }

    private function parseToken(
        string $raw,
        SourceSpan $span,
        string $tag,
        string $label,
    ): SemanticNodeInterface|ParseError {
        [$leftSide, $slug] = $this->splitTagAndSlug($tag);

        $parts = array_map(
            static fn (string $part): string => trim($part),
            explode(':', $leftSide)
        );

        if ($parts === [] || $parts[0] === '') {
            return new ParseError(
                message: 'Empty semantic tag kind.',
                span: $span,
                raw: $raw,
            );
        }

        $kind = mb_strtolower($parts[0]);

        if ($kind === 'fact') {
            return $this->parseFactToken(
                raw: $raw,
                sourceSpan: $span,
                parts: $parts,
                label: $label,
                slug: $slug,
            );
        }

        return $this->parseEntityReferenceToken(
            raw: $raw,
            sourceSpan: $span,
            parts: $parts,
            label: $label,
            slug: $slug,
        );
    }

    /**
     * @return array{0: string, 1: ?string}
     */
    private function splitTagAndSlug(string $tag): array
    {
        $parts = explode('|', $tag, 2);

        $leftSide = trim($parts[0]);
        $slug = isset($parts[1]) ? trim($parts[1]) : null;

        if ($slug === '') {
            $slug = null;
        }

        return [$leftSide, $slug];
    }

    /**
     * @param list<string> $parts
     */
    private function parseFactToken(
        string $raw,
        SourceSpan $sourceSpan,
        array $parts,
        string $label,
        ?string $slug,
    ): FactNode|ParseError {
        if ($slug !== null) {
            return new ParseError(
                message: 'Fact tags must not contain a slug reference.',
                span: $sourceSpan,
                raw: $raw,
            );
        }

        $factType = $parts[1] ?? null;
        $revealedDate = $parts[2] ?? null;

        if ($factType === null || $factType === '') {
            return new ParseError(
                message: 'Fact tag requires a fact type, e.g. [fact:alias](The Black Stag).',
                span: $sourceSpan,
                raw: $raw,
            );
        }

        if (count($parts) > 3) {
            return new ParseError(
                message: 'Fact tag has too many colon-separated segments.',
                span: $sourceSpan,
                raw: $raw,
            );
        }

        return new FactNode(
            raw: $raw,
            sourceSpan: $sourceSpan,
            factType: $factType,
            revealedDate: $revealedDate,
            value: $label,
        );
    }

    /**
     * @param list<string> $parts
     */
    private function parseEntityReferenceToken(
        string $raw,
        SourceSpan $sourceSpan,
        array $parts,
        string $label,
        ?string $slug,
    ): WorldEntityReferenceNode|ParseError {
        $kind = $parts[0];
        $predicate = $parts[1] ?? null;
        $revealedDate = $parts[2] ?? null;

        if (count($parts) > 3) {
            return new ParseError(
                message: 'Entity reference tag has too many colon-separated segments.',
                span: $sourceSpan,
                raw: $raw,
            );
        }

        return new WorldEntityReferenceNode(
            raw: $raw,
            sourceSpan: $sourceSpan,
            label: $label,
            kind: $kind,
            predicate: $predicate,
            revealedDate: $revealedDate,
            slug: $slug,
        );
    }
}
