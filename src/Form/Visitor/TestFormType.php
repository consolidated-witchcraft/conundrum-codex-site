<?php

namespace App\Form\Visitor;

use App\Form\Type\PostalAddressType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'test_text_field',
                TextType::class
            )
            ->add(
                'test_textarea',
                TextareaType::class,
                [
                    'attr' => [
                        'rows' => 10,
                        'columns' => 10
                    ]
                ]
            )
            ->add('test_email',
            EmailType::class)
            ->add(
                'test_select_type',
                ChoiceType::class,
                [
                    'expanded' => false,
                    'multiple' => false,
                    'choices' => [
                        'Lorem' => 'lorem',
                        'Ipsum' => 'ipsum',
                        'Dolor' => 'dolor',
                    ]
                ]
            )
            ->add(
                'test_checkbox',
                CheckboxType::class,
            )
            ->add(
                'test_expanded_choice',
                ChoiceType::class,
                [
                    'expanded' => false,
                    'multiple' => true,
                    'choices' => [
                        'Lorem' => 'lorem',
                        'Ipsum' => 'ipsum',
                        'Dolor' => 'dolor',
                    ]
                ]
            )
            ->add(
                'test_radios',
                ChoiceType::class,
                [
                    'expanded' => true,
                    'multiple' => false,
                    'choices' => [
                        'Lorem' => 'lorem',
                        'Ipsum' => 'ipsum',
                        'Dolor' => 'dolor',
                    ]
                ]
            )
            ->add(
                'test_checkboxes',
                ChoiceType::class,
                [
                    'expanded' => true,
                    'multiple' => true,
                    'choices' => [
                        'Lorem' => 'lorem',
                        'Ipsum' => 'ipsum',
                        'Dolor' => 'dolor',
                    ]
                ]
            )
            ->add('postalAddress', PostalAddressType::class)
            ->add(
                'submit',
                SubmitType::class,

            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
