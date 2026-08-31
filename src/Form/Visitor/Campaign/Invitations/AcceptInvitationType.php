<?php

declare(strict_types=1);

namespace App\Form\Visitor\Campaign\Invitations;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/** @extends AbstractType<null> */
class AcceptInvitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'confirm', type: CheckboxType::class, options: ['label' => 'Accept this invitation?'])
            ->add(child: 'accept', type: SubmitType::class);
    }
}
