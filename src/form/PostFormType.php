<?php
namespace App\form;

use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class PostFormType extends AbstractType

{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('name')
        ;
    }
}