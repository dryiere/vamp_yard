<?php

namespace App\Form;

use Symfony\Component\Form\FormBuilderInterface;

class PostFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('body');
    }
}