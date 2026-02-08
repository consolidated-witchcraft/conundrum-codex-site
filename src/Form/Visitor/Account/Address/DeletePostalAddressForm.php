<?php

declare(strict_types=1);

namespace App\Form\Visitor\Account\Address;

use App\Dto\Visitor\User\PostalAddressDto;
use App\Form\Type\PostalAddressType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeletePostalAddressForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder->add('confirm',CheckboxType::class,[
            'label' => 'Really Delete this address'
        ]);
        $builder->add(
            'submit',
            SubmitType::class,
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
