<?php

namespace App\Services\PuzzleInstance\Domain\Interfaces;

interface PuzzleInstanceProviderInterface
{
    public function generateNewInstanceCode(? string $candidateInstanceCode = null, int $attempts = 0): string;

}
