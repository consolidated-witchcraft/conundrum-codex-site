<?php

namespace App\ApiResource\DiceRoll\Providers;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\DiceRoll\Dto\DiceRollDto;
use App\ApiResource\DiceRoll\Exceptions\UnprocessableRollStringException;
use App\Validator\DiceRollConstraintValidator;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class DiceRollProvider implements ProviderInterface
{


    private function processRolls(string $rollState): array {
        $clauses = explode(',', $rollState);
        $rolls = [];
        foreach ($clauses as $clause) {
            $rolls[] = $this->generateRoll($clause);
        }
        return $rolls;
    }



    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!$uriVariables['rollState']) {
            throw new UnprocessableEntityHttpException("Could not interpret roll state.");
        }

        $decodedRollState = urldecode($uriVariables['rollState']);
        try {
            DiceRollConstraintValidator::validateRollState($decodedRollState);
        } catch (UnprocessableRollStringException $e) {
            throw new UnprocessableEntityHttpException($e->getMessage(), $e);
        }

        return $this->processRolls($decodedRollState);
    }

    private function generateRoll(string $rollClause): DiceRollDto {
        $bonus = 0;
        if (str_contains(needle: 'p', haystack: $rollClause)) {
            $components = explode('p', $rollClause);
            $bonus = end($components);
            $components = explode('d', reset($components));
            $numDice = $components[0];
            $dieSides = $components[1];
        } else {
            $components = explode('d', $rollClause);
            $numDice = $components[0];
            $dieSides = $components[1];
        }
        $rolled = self::rollDice($numDice, $dieSides);
        return new DiceRollDto(id: $rollClause, dieSides: $dieSides, roll: $rolled, total: $rolled+$bonus, bonus: $bonus);
    }

    public static function rollDice(int $numDice, int $dieSides): int {
        $total = 0;
        for ($i=1; $i<=$numDice; $i++) {
            $total+= rand(min: 1, max: $dieSides);
        }
        return $total;
    }
}
