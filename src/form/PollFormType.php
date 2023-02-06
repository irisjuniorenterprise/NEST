<?php

namespace App\form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;

class PollFormType extends AbstractType

{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('end', DateTimeType::class,['widget'=>'single_text'])
        ;
    }
}
{

}