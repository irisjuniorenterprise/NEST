<?php

namespace App\form;

use App\Entity\Department;
use App\Repository\DepartmentRepository;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Security;

/**
 * @property $security
 */
class PostFormType extends AbstractType

{
    private $security;
    private $departmentRepository;

    public function __construct(Security $security,DepartmentRepository $departmentRepository)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
        $this->departmentRepository = $departmentRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function ($event) {
                $form = $event->getForm();
                $data = $event->getData();
                // get the logged-in user
                $user = $this->security->getUser();
                // get the user's department
                $department = $user?->getDepartment();
                if (in_array('ROLE_'.$department, $user?->getRoles(), true)) {
                    // Get the manager's department
                    $department = $user?->getDepartment();
                    // Remove all other departments from the form
                    $choices = $this->departmentRepository->findAll();
                    $choices = array_filter($choices, function ($choice) use ($department) {
                        return $choice->getName() === 'All' || $choice->getId() === $department->getId();
                    });
                }else{
                    $choices = $this->departmentRepository->findAll();
                }
                $form->add('name');
                $form->add('departments', EntityType::class, [
                    'class' => Department::class,
                    'choices' => $choices,
                    'allow_extra_fields' => true,
                    'multiple' => true,
                    'expanded' => true,
                    'by_reference' => false,
                ]);
            })
            ->add('name')
            ->add('departments', EntityType::class, [
                'class' => Department::class,
                'allow_extra_fields' => true,
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false,
            ])
        ;
    }
}