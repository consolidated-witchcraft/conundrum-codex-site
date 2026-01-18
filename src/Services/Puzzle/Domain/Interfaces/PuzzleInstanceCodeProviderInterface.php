<?php

namespace App\Services\Puzzle\Domain\Interfaces;

interface PuzzleInstanceCodeProviderInterface
{
    public function decollideInstanceCode(string $candidateInstanceCode, int $attempts = 0): string;

}
