<?php

namespace App\Controller\nest;

use App\Repository\BlameRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/eagle', name: 'app_eagles')]
    public function index(UserRepository $userRepository ): Response
    {
        return $this->render('eagles/index.html.twig', [
            'eagles' => $userRepository->findAll(),
        ]);
    }
    #[Route('/blame', name: 'app_blames')]
    public function blameList(BlameRepository $blameRepository ): Response
    {
        $userRole=$this->getUser()->getRoles()[0];
        $department=explode('_',$userRole)[1];
        if ($userRole==='ROLE_HR' || $userRole==='ROLE_VP' || $userRole==='ROLE_PRESIDENT' ){
            $blames=$blameRepository->findAll();
        }
        else{
            $blames=$blameRepository->findByAdminRole($department);
        }
        return $this->render('blame/index.html.twig', [
            'blames' => $blames
        ]);
    }

}
