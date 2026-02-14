<?php

namespace App\Form\Admin\Users;

use App\Dto\Admin\User\AdminUserDto;
use App\Services\User\Domain\Role;
use App\Services\User\Domain\UserFeat;
use App\Services\User\Domain\ValueObjects\UserAccountType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;

class AdminUserEditType extends AbstractType
{
    public const array VALID_IMAGE_TYPES = ['jpeg', 'jpg', 'png', 'webp'];
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('avatar', FileType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'Avatar',
                'attr' => [
                    'accept' => 'image/jpeg, image/png, image/webp',
                ],
                'constraints' => [
                    new File(
                        maxSize: '5m',
                        extensions: self::VALID_IMAGE_TYPES,
                        extensionsMessage: 'Please upload a valid image file. Valid filestypes are:',
                    )
                ],
            ])
            ->add(
                'feats',
                EntityType::class,
                [
                    'class' => UserFeat::class,
                    'multiple' => true,
                    'expanded' => true,
                ]
            )
            ->add('email')
            ->add('username')
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('acceptedCookies', CheckboxType::class, [
                'required' => false,
                'label' => 'Accepted Cookies?',
            ])
            ->add('profilePublic', CheckboxType::class, [
                'required' => false,
                'label' => 'Public Profile?',
            ])
            ->add('roles', EntityType::class, [
                'class' => Role::class,
                'label' => 'Select at least one role',
                'choice_label' => 'label',
                'multiple' => true,
                'expanded' => true
            ])
            ->add('userAccountType', EntityType::class, [
                'class' => UserAccountType::class,
                'label' => 'User Account Type',
                'choice_label' => 'label',
                'multiple' => false,
                'expanded' => true
            ])
            ->add('submit', SubmitType::class, ['label' => 'Save Changes']);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AdminUserDto::class,
        ]);
    }
}
