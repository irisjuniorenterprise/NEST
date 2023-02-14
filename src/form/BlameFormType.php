<?php

namespace App\form;


use App\Entity\Blame;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\NotBlank;

class BlameFormType extends AbstractType
{
    private $security;
    private $userRepository;

    public function __construct(UserRepository $userRepository, Security $security)
    {
        $this->userRepository = $userRepository;
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $roles  = ['ROLE_IT','ROLE_BUSINESS','ROLE_DEVCO','ROLE_MARKETING'];
        if (!in_array($this->security->getUser()?->getRoles()[0], $roles, true)) {
            $choices = $this->userRepository->findAll();
        }else{
        $choices = $this->userRepository->findByDepartments([$this->security->getUser()?->getDepartment()]);
        }
        // remove the current user from the list of choices
        $choices = array_filter($choices, function ($choice) {
            return $choice->getId() !== $this->security->getUser()?->getId();
        });
        $builder
            ->add('eagle', EntityType::class, [
                'class' => User::class,
                'choices' => $choices,
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