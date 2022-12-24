<?php

namespace App\Controller\nest;

use App\Repository\BiblioIRISRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LibraryController extends AbstractController
{
    #[Route('/library', name: 'app_library')]
    public function index(BiblioIRISRepository $biblioIRISRepository): Response
    {
        return $this->render('library/index.html.twig', [
            'resources' => $biblioIRISRepository->findAll(),
        ]);
    }
}
