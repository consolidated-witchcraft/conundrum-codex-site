<?php

namespace App\Form\Visitor\Account;

use App\Dto\Visitor\User\UserEditDto;
use App\Dto\Visitor\User\UserPreferencesEditDto;
use App\Form\Type\UserPreferencesEditType;
use App\Services\User\Domain\ValueObjects\PreferredFontStack;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UserEditType extends AbstractType
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
            ->add('profilePublic', checkboxType::class, [
                'required' => false,
                'label' => 'Public Profile?',
                'help' => 'If you make your profile public, other users can view your name, avatar and Feats. Other users can never view your e-mail address.'
            ])
            ->add('userPreferences', UserPreferencesEditType::class, [
            ])
            /*->add('acceptedCookies', checkboxType::class, [
                'required' => false,
                'label' => 'Accept Cookies?',
                'help' => 'If you mark Cookies as accepted, you won\'t see the cookie warning message again (provided you\'re logged in)'
            ])
            ->add('preferredFontStack', ChoiceType::class, [
                'required' => true,
                'label' => 'Preferred Font',
                'help' => 'Set a more comfortable font for the user interface.',
                'choices' => array_flip(PreferredFontStack::getAvailableFontStacks())
            ])*/
            ->add('userName', TextType::class)
            ->add('emailAddress', EmailType::class, [])
            ->add('submit', SubmitType::class, ['label' => 'Save Changes'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserEditDto::class,
        ]);
    }
}
