<?php

declare(strict_types=1);

namespace App\Form\EventManager;

use App\Dto\EventManager\OrganisationDto;
use App\Services\User\Domain\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganisationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
            ])
            ->add('handle', TextType::class, [
            ])
            ->add('organisationLogo', FileType::class, [
                'label' => 'Logo',
                'mapped' => false,
            ])
            ->add('managers', EntityType::class, [
                'class' => User::class,
                'label' => 'Managers',
                'choice_label' => 'email',
                'multiple' => true,
                'expanded' => false
            ])
            ->add('submit', SubmitType::class, []);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OrganisationDto::class
        ]);
    }
}
