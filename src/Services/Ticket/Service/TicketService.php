<?php

namespace App\Services\Ticket\Service;

use App\Services\Core\Service\AbstractDomainEntityService;
use App\Services\Orders\Domain\Exceptions\UnknownProductTypeException;
use App\Services\Orders\Domain\Product;
use App\Services\Orders\Domain\ProductStock;
use App\Services\Orders\Domain\ValueObjects\ProductType;
use App\Services\Orders\Service\Interfaces\ProductServiceInterface;
use App\Services\Ticket\Domain\TicketType;
use App\Services\Ticket\Domain\TicketTypeFormat;
use App\Services\Ticket\Dto\TicketTypeDto;
use App\Services\Ticket\Service\Exception\InvalidTicketTypeDefinitionException;
use App\Services\Ticket\Service\Interfaces\TicketServiceInterface;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\Events\Domain\Event;
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
     * @return TicketType
     *
     * @throws UnknownProductTypeException
     * @throws InvalidTicketTypeDefinitionException
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
        $formats = new ArrayCollection();

        foreach ($ticketTypeDto->ticketFormats as $ticketFormatSlug) {
            $format = new TicketTypeFormat(
                name: sprintf("%s %s", $ticketTypeDto->name, $ticketFormatSlug),
                slug: $ticketFormatSlug,
            );

            $productType = ProductType::makeFromHandle($ticketFormatSlug);
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
            $formats->add($format);
            $this->productService->saveProduct($product, false);
        }

        $ticketType = new TicketType(
            name: $ticketTypeDto->name,
            slug: $ticketTypeDto->slug,
            description: $ticketTypeDto->description,
            priceCentessimal: $ticketTypeDto->priceCentessimal,
            ticketTypeFormats: $formats,
            event: $event,
        );
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
