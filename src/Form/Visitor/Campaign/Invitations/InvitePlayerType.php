<?php

declare(strict_types=1);

namespace App\Form\Visitor\Campaign\Invitations;

use App\Dto\Visitor\Campaign\Invitations\InvitePlayerDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** @extends AbstractType<InvitePlayerDto> */
class InvitePlayerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'email', type: EmailType::class)
            ->add(child: 'invitationMessage', type: TextType::class, options: ['label' => 'Invitation message'])
            ->add(child: 'submit', type: SubmitType::class, options: ['label' => 'Invite player']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(defaults: ['data_class' => InvitePlayerDto::class]);
    }
}
