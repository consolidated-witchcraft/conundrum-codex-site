<?php

namespace App\Services\Ticket\Service;

use App\Services\Core\Service\AbstractDomainEntityService;
use App\Services\Events\Domain\Event;
use App\Services\Orders\Domain\Exceptions\UnknownProductTypeException;
use App\Services\Orders\Domain\ProductStock;
use App\Services\Products\Domain\Product;
use App\Services\Products\Domain\ValueObjects\ProductType;
use App\Services\Products\Service\Interfaces\ProductServiceInterface;
use App\Services\Ticket\Domain\Exceptions\InvalidTicketTypeDefinitionException;
use App\Services\Ticket\Domain\Exceptions\UnknownTicketFormatException;
use App\Services\Ticket\Domain\TicketType;
use App\Services\Ticket\Domain\TicketTypeFormat;
use App\Services\Ticket\Domain\ValueObjects\TicketFormat;
use App\Services\Ticket\Dto\TicketTypeDto;
use App\Services\Ticket\Service\Interfaces\TicketServiceInterface;
use App\Services\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TicketService extends AbstractDomainEntityService implements TicketServiceInterface
{

    private const string SKU_PATTERN = "%s.%s.%s.%s";

    public function __construct(
        private readonly ProductServiceInterface $productService,
        private readonly ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
    ) {
        parent::__construct($entityManager);
    }

    /**
     * @param TicketTypeDto $ticketTypeDto
     * @param Event $event
     * @param User $creatingUser
     * @param bool $flush
     * @return TicketType
     *
     * @throws InvalidTicketTypeDefinitionException
     * @throws UnknownProductTypeException
     * @throws UnknownTicketFormatException
     */
    public function generateTicketTypeFromDto(TicketTypeDto $ticketTypeDto, Event $event, User $creatingUser, bool $flush = true): TicketType
    {
        $violations = $this->validator->validate($ticketTypeDto);
        $errors = [];
        $success= true;

        if ($violations->count() > 0) {
            $success = false;
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }
        }
        if (!$success) {
            throw new InvalidTicketTypeDefinitionException($errors);
        }
        $productStock = new ProductStock(
            initialStockLevel: $ticketTypeDto->initialQuantity,
            currentStockLevel: $ticketTypeDto->initialQuantity,
        );

        $ticketType = new TicketType(
            name: $ticketTypeDto->name,
            slug: $ticketTypeDto->slug,
            description: $ticketTypeDto->description,
            priceCentessimal: $ticketTypeDto->priceCentessimal,
            event: $event,
        );
        $productType = ProductType::makeFromHandle('ticket');
        foreach ($ticketTypeDto->ticketTypeFormats as $ticketFormatSlug) {
            $format = new TicketTypeFormat(
                TicketFormat::makeFromHandle($ticketFormatSlug),
            );


            $product = new Product(
                sku: self::generateSku(
                    $event->getOrganisation()->getHandle(),
                    $event->getEventSlug(),
                    $ticketTypeDto->slug,
                    $ticketFormatSlug
                ),
                name: sprintf("%s %s", $ticketTypeDto->name, $productType->getName()),
                type: $productType,
                description: $ticketTypeDto->description,
                preTaxPriceCentessimal: $ticketTypeDto->priceCentessimal,
                createdBy: $creatingUser
            );
            $format->setProduct($product);
            $productStock->addProduct($product);
            $ticketType->addTicketTypeFormat($format);
            $this->productService->saveProduct($product, false);
        }


        $this->entityManager->persist($ticketType);
        if ($flush) {
            $this->entityManager->flush();
        }
        return $ticketType;

    }

    public static function generateSku(
        string $organisationHandle,
        string $eventSlug,
        string $ticketTypeSlug,
        string $ticketFormatSlug,
    ): string {
        return strtoupper(sprintf(
            self::SKU_PATTERN,
            $organisationHandle,
            $eventSlug,
            $ticketTypeSlug,
            $ticketFormatSlug
        ));
    }
}
