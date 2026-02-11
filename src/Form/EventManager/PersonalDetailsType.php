<?php

declare(strict_types=1);

namespace App\Form\EventManager;

use App\Dto\Visitor\Events\PersonalDetailsDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonalDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customerEmail', EmailType::class, [
            ])
            ->add('customerName', TextType::class, [
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Proceed to payment'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PersonalDetailsDto::class
        ]);
    }
}
