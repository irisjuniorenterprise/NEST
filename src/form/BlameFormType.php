<?php

namespace App\form;


use App\Entity\Blame;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class BlameFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('eagle', EntityType::class, [
                'class' => User::class,
                'required' => true,
                'placeholder'=>'choose eagle',
                'error_bubbling' => true,
            ])
            ->add('reason', TextareaType::class,[
                    'required'=>true,
                    'error_bubbling' => true,
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            );
    }

    }