<?php

namespace App\Form\Admin;

use App\Form\Type\SlidingSwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class FeatureSwitcherToggleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('enabled', CheckboxType::class, [
            'required' => false,
        ])->add('submit', SubmitType::class, []);
    }
}
