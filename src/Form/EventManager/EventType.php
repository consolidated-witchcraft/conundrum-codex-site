<?php

declare(strict_types=1);

namespace App\Form\EventManager;

use App\Dto\EventManager\EventDto;
use App\Dto\EventManager\OrganisationDto;
use App\Form\Type\PostalAddressType;
use App\Services\User\Domain\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('eventSlug', TextType::class)
            ->add('description', TextareaType::class)
            ->add('heroImage', FileType::class, [
                'mapped' => false,
                'attr' => [
                    'accept' => 'image/jpeg, image/png, image/webp',
                ]
            ])
            ->add('location', PostalAddressType::class,
            [
                'label' => 'Location'
            ])
            ->add(
                'prebookOnly',
            ChoiceType::class,
                [
                    'choices' => [
                        'Yes' => true,
                        'No' => false,
                    ],
                    'expanded' => true,
                    'help' => 'If an event is preBook only, prebooked tickets are the only way to enter on the day.'
                ]
            )
            ->add('startDate', DateTimeType::class, [
                'html5' => true,
            ])
            ->add('endDate', DateTimeType::class, [
                'html5' => true,
            ])
            ->add('submit', SubmitType::class, []);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventDto::class
        ]);
    }
}
