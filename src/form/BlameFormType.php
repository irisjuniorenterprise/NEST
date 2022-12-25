<?php

namespace App\form;


use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class BlameFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('eagle', EntityType::class, [
                'class' => User::class,
                'required' => true,
                'placeholder'=>'choose eagle'
            ])
            ->add('reason', TextareaType::class,[
                    'required'=>true,
                ]
            );
    }

}