<?php

declare(strict_types=1);

namespace App\Form\Admin\Files;

use App\Dto\Admin\File\AdminFileDto;
use App\Dto\Admin\Quotation\QuotationDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file',
            FileType::class,
            [
                'mapped' => false,
                'required' => false,
            ])
            ->add('title')
            ->add('description', TextAreaType::class, [])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AdminFileDto::class
        ]);
    }
}
