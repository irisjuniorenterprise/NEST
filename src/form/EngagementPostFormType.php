<?php
namespace App\form;

use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class EngagementPostFormType extends AbstractType

{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('place')
            ->add('link')
            ->add('start', DateTimeType::class,['widget'=>'single_text'])
            ->add('end', DateTimeType::class,['widget'=>'single_text'])
        ;
    }
}