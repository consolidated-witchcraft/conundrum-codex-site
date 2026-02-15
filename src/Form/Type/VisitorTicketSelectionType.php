<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Dto\Visitor\Events\TicketPurchaseDto;
use App\Dto\Visitor\Events\TicketTypeFormatDto;
use App\Dto\Visitor\Events\VisitorTicketTypeDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VisitorTicketSelectionType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**
         * @var VisitorTicketTypeDto $data;
         */
        $data = $options['data'];

        $ticketTypeSlug = $data->slug;

        $namePattern = sprintf(" %s - £%s exc. Tax", $data->name, ($data->priceCentessimal/100));
        $builder->add(
            'quantity',
            NumberType::class,
            [
                'label' => $namePattern,
                'required' => false,
                'attr' => [
                    'min' => 0,
                    'max' => 10,
                    'class' => 'number-spinner',
                    'is' => 'number-spinner',
                    'step' => 1,
                    'length' => 2,
                    'placeholder' => 1,
                ],
                'data' => 0,
                'html5' => true,
                'help' => $data->description,
                'mapped' => true,
            ]
        );
        $builder->add(
            "format",
            ChoiceType::class,
            [
                'label' => "Ticket Format",
                'choices' => self::mapFormatsToChoices($data->ticketTypeFormats),
            ]
        );
    }


    /**
     * @param array<string, TicketTypeFormatDto> $formats
     * @return array
     */
    public static function mapFormatsToChoices(array $formats): array {
        $choiceArray = [

        ];
        foreach ($formats as $format) {
            $choiceArray[$format->ticketTypeFormatName] = $format->ticketTypeFormatSku;
        }
        return $choiceArray;
    }

    public function mapDataToForms(mixed $viewData, \Traversable $forms): void
    {
        if (!$viewData instanceof VisitorTicketTypeDto) {
            return;
        }
        $forms = iterator_to_array($forms);
        foreach ($forms as $key => $form) {
            switch ($key) {
                default:
                    dd($key);

            }
        }
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        $forms = iterator_to_array($forms);
        foreach ($forms['ticketTypes']->getData() as $key => $datum) {
            $strippedKey = str_replace('_quantity', '', $key);
            $viewData->ticketTypes[$strippedKey]['quantity'] = (int) $datum;
        }
    }
}
