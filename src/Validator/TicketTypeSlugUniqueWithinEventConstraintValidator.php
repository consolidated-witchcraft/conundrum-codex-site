<?php

declare(strict_types=1);

namespace App\Validator;

use App\Services\Events\Domain\TicketType;
use App\Services\Events\Service\EventServiceInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class TicketTypeSlugUniqueWithinEventConstraintValidator extends ConstraintValidator
{
    public function __construct(private readonly EventServiceInterface $eventService) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof TicketTypeSlugUniqueWithinEventConstraint) {
            throw new UnexpectedTypeException($constraint, TicketTypeSlugUniqueWithinEventConstraint::class);
        }

        if (null !== $value && !is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        /**
         * @var TicketType $data
         */
        $data = $this->context->getRoot();

        $ticketType = $this->eventService->getTicketTypeByEventAndSlug($data->getEvent(), $data->getSlug());

        if ($ticketType !== null && $data->getId() !== $ticketType->getId()) {
            $this->context->buildViolation(sprintf("The ticket type slug '%s' is not unique within the event.", $data->getSlug()))
                ->addViolation();
        }
    }
}
