<?php

declare(strict_types=1);

namespace App\Form\Visitor\Campaign\Invitations;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/** @extends AbstractType<null> */
class RevokeInvitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'confirm', type: CheckboxType::class, options: [
                'label' => 'Confirm this action',
                'help' => 'This invitation will no longer be usable.',
                'required' => true,
            ])
            ->add(child: 'submit', type: SubmitType::class);
    }
}
