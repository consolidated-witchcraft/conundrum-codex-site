<?php

declare(strict_types=1);

namespace App\Form\EventManager;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class OrganisationDeactivationConfirmationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'confirm',
            CheckboxType::class,
            [
                'label' => "Are you sure you want to deactivate this organisation?",
                'required' => true,
                'help' => "Deleting an organisation deactivates all its events. This action cannot be undone!",
            ]
        )->add(
            'submit',
            SubmitType::class,
            [
                'label' => 'Deactivate Organisation',
                'attr' => [
                    'class' => 'btn btn-danger',
                ]
            ]
        );
    }
}
