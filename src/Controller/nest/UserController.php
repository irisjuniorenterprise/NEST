<?php

namespace App\Controller\nest;

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
}
