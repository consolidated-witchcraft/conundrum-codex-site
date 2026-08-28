<?php

namespace App\Form\Visitor;

use App\Dto\Visitor\User\RegisterUserDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;

class RegistrationForm extends AbstractType
{
    public function __construct(
        private readonly bool $recaptchaEnabled,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'userName',
                TextType::class,
                [
                    'label' => 'Your name',
                ]
            )
            ->add(
                'emailAddress',
                EmailType::class
            )

            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add(
                'invitationCode',
                TextType::class,
                [
                    'required' => false,
                    'help' => 'Enter an invitation code to join a game as soon as you\'ve registered!'
                ]
            )
            ->add(
                'profilePublic',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'Make your profile public?',
                    'help' => 'If you make your profile public, other users can view your name, avatar and Feats. Other users can never view your e-mail address.'
                ]
            )
            ->add(
                'acceptCookies',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'Accept Cookies',
                    'help' => 'If you mark Cookies as accepted, you won\'t see the cookie warning message again (provided you\'re logged in)'
                ]
            )

            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'I agree to abide to the code of conduct',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class)
        ;

        if ($this->recaptchaEnabled) {
            $builder->add(
                'captcha',
                Recaptcha3Type::class,
                [
                    'constraints' => new Recaptcha3(),
                    'action_name' => 'homepage',
                    'locale' => 'en',
                ]
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RegisterUserDto::class,
        ]);
    }
}
