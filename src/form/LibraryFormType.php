<?php
namespace App\form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class LibraryFormType extends AbstractType

{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('content', TextareaType::class)
        ;
    }
}