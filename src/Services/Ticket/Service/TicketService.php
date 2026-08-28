<?php

declare(strict_types=1);

namespace App\Services\Ticket\Service;

use App\Services\Core\Service\AbstractDomainEntityService;
use App\Services\Events\Domain\Event;
use App\Services\Orders\Domain\Exceptions\UnknownProductTypeException;

use App\Services\Product\Domain\Product;
use App\Services\Product\Domain\ProductVariant;
use App\Services\Product\Domain\ProductVariantStock;
use App\Services\Product\Domain\ValueObjects\ProductType;
use App\Services\Product\Service\Interfaces\ProductServiceInterface;
use App\Services\Ticket\Domain\Exceptions\InvalidTicketTypeDefinitionException;
use App\Services\Ticket\Domain\Exceptions\UnknownTicketFormatException;
use App\Services\Ticket\Domain\TicketType;
use App\Services\Ticket\Domain\TicketTypeProductVariantMap;
use App\Services\Ticket\Domain\ValueObjects\TicketFormat;
use App\Services\Ticket\Dto\TicketTypeDto;
use App\Services\Ticket\Service\Interfaces\TicketServiceInterface;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;
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
                $errors[$violation->getPropertyPath()] = (string) $violation->getMessage();
            }
        }
        if (!$success) {
            throw new InvalidTicketTypeDefinitionException($errors);
        }

        $name = $ticketTypeDto->name;
        $slug = $ticketTypeDto->slug;
        $description = $ticketTypeDto->description;
        $priceCentessimal = $ticketTypeDto->priceCentessimal;
        $initialQuantity = $ticketTypeDto->initialQuantity;
        if ($name === null || $slug === null || $description === null || $priceCentessimal === null || $initialQuantity === null) {
            throw new InvalidTicketTypeDefinitionException([
                'ticketType' => 'The validated ticket type contains an incomplete definition.',
            ]);
        }

        $ticketType = new TicketType(
            name: $name,
            slug: $slug,
            description: $description,
            priceCentessimal: $priceCentessimal,
            event: $event,
        );
        $product = new Product(
            baseSku: self::generateSku(
                event: $event,
                ticketTypeSlug: $slug,
                ticketFormatSlug: 'BASE'
            ),
            name: $name,
            organisation: $event->getOrganisation(),
            description: $description,
        );

        /**
         * Physical and E-Ticket formats share a common stock.
         */
        $productVariantStock = new ProductVariantStock(
            stockLevel: $initialQuantity,
        );
        $ticketTypeProductVariantMaps = new ArrayCollection();
        $productVariants = new ArrayCollection();
        $delta = 0;
        foreach ($ticketTypeDto->ticketTypeFormats as $ticketFormatSlug) {
            $ticketTypeProductVariantMap = new TicketTypeProductVariantMap(
                ticketType: $ticketType,
            );
            $productVariantType = ProductType::makeFromHandle($ticketFormatSlug);
            $productVariant = new ProductVariant(
                sku: self::generateSku(
                    event: $event,
                    ticketTypeSlug: $slug,
                    ticketFormatSlug: $ticketFormatSlug,
                ),
                productVariantName: sprintf("%s %s", $name, $productVariantType->getName()),
                type: $productVariantType,
                delta: $delta,
                preTaxPriceCentessimal: $priceCentessimal,
                createdBy: $creatingUser,
                taxExempt: false,
                requiresPostage: $productVariantType->getHandle() === 'physical_ticket',
                publicationDate: new \DateTime(),
                productVariantStock: $productVariantStock,
                product: $product,
            );
            $productVariants->add($productVariant);
            $ticketTypeProductVariantMap->setProductVariant($productVariant);
            $ticketTypeProductVariantMaps->add($ticketTypeProductVariantMap);

            $delta++;
        }
        $product->setProductVariants($productVariants);
        $this->productService->saveProduct($product, false);
        $ticketType->setTicketTypeProductVariantMaps($ticketTypeProductVariantMaps);

        $this->entityManager->persist($ticketType);
        if ($flush) {
            $this->entityManager->flush();
        }
        return $ticketType;

    }

    public static function generateSku(
        Event $event,
        string $ticketTypeSlug,
        string $ticketFormatSlug,
    ): string {
        return strtoupper(sprintf(
            self::SKU_PATTERN,
            $event->getOrganisation()->getHandle(),
            $event->getEventSlug(),
            $ticketTypeSlug,
            $ticketFormatSlug
        ));
    }
}
