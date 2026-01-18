<?php

declare(strict_types=1);

namespace App\Validator;

use App\State\Exceptions\UnprocessableRollStateException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class DiceRollConstraintValidator extends ConstraintValidator
{
    private const string CLAUSE_PATTERN = '/^(?:100|[1-9][0-9]?)d(?:100|[1-9][0-9]?)(?:p(?:100|[1-9][0-9]?))?$/';

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof DiceRollConstraint) {
            throw new UnexpectedTypeException($constraint, DiceRollConstraint::class);
        }

        if (null !== $value && !is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        try {
            self::validateRollState($value);
        } catch (UnprocessableRollStateException $e) {
            $this->context->buildViolation($e->getMessage())
                ->addViolation();
        }
    }

    /**
     * @param string $rollState
     * @return void
     */
    public static function validateRollState(string $rollState): void {
        $clauses = explode(',', $rollState);
        foreach ($clauses as $clause) {
            if (!preg_match(self::CLAUSE_PATTERN, $clause)) {
                throw new UnprocessableRollStateException("Invalid string: must match 'xdy' or 'xdypz' where x,y and z are 1–100.");
            }
        }
    }
}
