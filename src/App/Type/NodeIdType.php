<?php

namespace App\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;

class NodeIdType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(), 
                    new Assert\Uuid([
                        "strict"    => true,
                    ]),
                ]
            ])
        ;
    }
}

