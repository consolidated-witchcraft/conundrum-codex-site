<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Dto\Visitor\Checkout\UserCartLineDto;
use App\Dto\Visitor\User\UserPreferencesEditDto;
use App\Services\User\Domain\ValueObjects\PreferredFontStack;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPreferencesEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder->add(
            'hasAcceptedCookies',
            CheckboxType::class,
            [
                'label' => 'Accept Cookies',
                'required' => false,
            ]
        )->add(
            'profilePublic',
            CheckboxType::class,
            [
                'label' => 'Make Profile Public',
                'required' => false,
            ]
        )->add(
            'preferredFontStack',
            ChoiceType::class,
            [
                'label' => 'Preferred Font',
                'choices' => PreferredFontStack::getAvailableFontStacks(),
                'help' => 'Set your preferred font. Available options include Regular (The default), Sans-serif and Dyslexic',
                'choice_label' => function (PreferredFontStack $preferredFontStack) {
                    return $preferredFontStack->getLabel();
                }
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserPreferencesEditDto::class,
        ]);
    }
}
