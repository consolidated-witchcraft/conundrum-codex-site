<?php

namespace App\Services\PuzzleInstance\Domain\Interfaces;

interface PuzzleInstanceCodeProviderInterface
{
    public function decollideInstanceCode(string $candidateInstanceCode, int $attempts = 0): string;

    public function generateNewInstanceCode(? string $candidateInstanceCode, int $attempts = 0): string;

}
