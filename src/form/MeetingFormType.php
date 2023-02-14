<?php

namespace App\form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class MeetingFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('type',ChoiceType::class,[
                'choices' => [
                    'Réunion générale' => 'Réunion générale',
                    'Assemblée générale' => 'Assemblée générale',
                    'Assemblée générale ordinaire' => 'Assemblée générale ordinaire',
                    'Assemblée générale extraordinaire' => 'Assemblée générale extraordinaire',
                    'Assemblée générale législatif' => 'Assemblée générale législatif',
                    'Assemblée générale électif' => 'Assemblée générale électif',
                    'Réunion Statutaire' => 'Réunion Statutaire',
                    'Réunion département' => 'Réunion département',
                ],
                'label' => 'Meeting type',
                'placeholder' => 'Choose a meeting type',
                'multiple' => false,
                'expanded' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
        ;
    }
}