<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Dto\EventManager\TicketTypeDto;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void {

        $builder->add(
            'id',
            HiddenType::class,
            [
                'required' => false,
            ]
        )
        ->add(
            'name',
            TextType::class,
            [
                'help' => 'Enter a name for your ticket type, e.g: \'Child\', \'Adult\', \'VIP\', etc'
            ]
        )
        ->add('slug', TextType::class, [
            'help' => 'A ticket type slug'
        ])
        ->add(
            'description',
            TextareaType::class,
            [
                'help' => 'A brief description for this ticket type.',
                'attr' => [
                    'rows' => 10,
                ]
            ]
        )
        ->add(
            'priceCentessimal',
            MoneyType::class,
            [
                'label' => 'Price',
                'html5' => true,
                'help' => 'Enter the cost of a ticket of this type.',
                'currency' => 'GBP',
                'divisor' => 100,
            ]
        );
        $builder->setDataMapper($this);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TicketTypeDto::class
        ]);
    }

    public function mapDataToForms(mixed $viewData, \Traversable $forms): void
    {
        if (null === $viewData) {
            return;
        }
        if (!$viewData instanceof TicketTypeDto) {
            return;
        }
        foreach ($forms as $key => $form) {
            switch ($key) {
                case 'id':
                    $form->setData($viewData->id);
                    break;
                case 'name':
                    $form->setData($viewData->name);
                    break;
                case 'slug':
                    $form->setData($viewData->slug);
                    break;
                case 'description':
                    $form->setData($viewData->description);
                    break;
                case 'priceCentessimal':
                    $form->setData($viewData->priceCentessimal);
                    break;
            }
        }
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        $forms = iterator_to_array($forms);

        $viewData = new TicketTypeDto(
            name: $forms['name']->getData(),
            slug: $forms['slug']->getData(),
            description: $forms['description']->getData(),
            priceCentessimal: (int) ($forms['priceCentessimal']->getData()),
            id: (int) $forms['id']->getData(),
        );
    }
}
