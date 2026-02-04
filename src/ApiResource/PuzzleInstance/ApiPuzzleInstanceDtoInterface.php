<?php

namespace App\ApiResource\PuzzleInstance;

interface ApiPuzzleInstanceDtoInterface
{
    public function getInstanceCode(): string;

    public function getGameSlug(): string;
}
