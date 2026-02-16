<?php

namespace App\Form\Type;

use App\Dto\Visitor\User\PostalAddressDto;
use App\Dto\Visitor\User\PostalAddressFieldDto;
use App\Services\PostalAddress\Domain\ValueObjects\IsoCountry;
use App\Services\PostalAddress\Service\PostalAddressServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PostalAddressType extends AbstractType implements DataMapperInterface
{
    public function __construct(
        private readonly PostalAddressServiceInterface $postalAddressService
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'id',
            HiddenType::class,
        );
        $builder->add(
            'label',
            TextType::class,
            [
                'label' => 'Address label',
                'help' => 'Give this address a name, such as \'Home Address\'',
            ]
        );
        $builder->add(
            'addressLineOne',
            TextType::class
        );
        $builder->add(
            'addressLineTwo',
            TextType::class,
            [
                'required' => false,
            ]
        );
        $builder->add(
            'townCity',
            TextType::class,
            [
                'label' => 'Town/City',
            ]
        );
        $builder->add(
            'countyProvince',
            TextType::class,
            [
                'label' => 'County/Province'
            ]
        );
        $builder->add(
            'countryCode',
            ChoiceType::class,
            [
                'label' => 'Country',
                'choices' => $this->postalAddressService->getCountryCodeList(),
                'choice_label' => function(IsoCountry $choice) {
                    return $choice->getName();
                }

            ]
        );
        $builder->add(
            'postalZip',
            TextType::class,
            [
                'label' => 'Postal/Zip Code',
            ]
        );
        $builder->add(
            'isDefaultBillingAddress',
            CheckboxType::class,
            [
                'label' => 'Make default billing address',
                'required' => false,
            ]
        );
        $builder->add(
            'isDefaultShippingAddress',
            CheckboxType::class,
            [
                'label' => 'Make default shipping address',
                'required' => false,
            ]
        );
        $builder->setDataMapper($this);
    }

    /**
     * @param mixed $viewData
     * @param \Traversable $forms
     * @return void
     */
    public function mapDataToForms(mixed $viewData, \Traversable $forms): void
    {
        if (!$viewData instanceof PostalAddressFieldDto) {
            return;
        }
        $forms = iterator_to_array($forms);

        foreach ($forms as $key => $form) {
            switch ($key) {
                case 'id':
                    $form->setData($viewData->id);
                    break;
                case 'label':
                    $form->setData($viewData->label);
                    break;
                case 'addressLineOne':
                    $form->setData($viewData->addressLineOne);
                    break;
                case 'addressLineTwo':
                    $form->setData($viewData->addressLineTwo);
                    break;
                case 'townCity':
                    $form->setData($viewData->townCity);
                    break;
                case 'countyProvince':
                    $form->setData($viewData->countyProvince);
                    break;
                case 'countryCode':
                    $form->setData($viewData->countryCode);
                    break;
                case 'postalZip':
                    $form->setData($viewData->postalZip);
                    break;
                case 'isDefaultBillingAddress':
                    $form->setData($viewData->isDefaultBillingAddress);
                    break;

                case 'isDefaultShippingAddress':
                    $form->setData($viewData->isDefaultShippingAddress);
                    break;
            }
        }
    }

    /**
     * @param \Traversable $forms
     * @param mixed $viewData
     * @return void
     */
    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        $forms = iterator_to_array($forms);

        $viewData = new PostalAddressFieldDto(
            label: $forms['label']->getData(),
            addressLineOne: $forms['addressLineOne']->getData(),
            townCity: $forms['townCity']->getData(),
            countyProvince: $forms['countyProvince']->getData(),
            countryCode: $forms['countryCode']->getData(),
            postalZip: $forms['postalZip']->getData(),
            isDefaultShippingAddress: $forms['isDefaultShippingAddress']->getData(),
            isDefaultBillingAddress: $forms['isDefaultBillingAddress']->getData(),
            addressLineTwo: $forms['addressLineTwo']->getData(),
            id: $forms['id']->getData()

        );

    }
}
