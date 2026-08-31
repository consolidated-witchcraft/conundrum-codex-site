<?php

declare(strict_types=1);

namespace App\Form\Visitor\Campaign\Invitations;

use App\Dto\Visitor\Campaign\Invitations\RedeemInvitationDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** @extends AbstractType<RedeemInvitationDto> */
class RedeemInvitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'email', type: EmailType::class, options: ['label' => 'E-mail address'])
            ->add(child: 'invitationCode', type: TextType::class, options: ['label' => 'Campaign invitation code'])
            ->add(child: 'submit', type: SubmitType::class, options: ['label' => 'Join campaign']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(defaults: ['data_class' => RedeemInvitationDto::class]);
    }
}
