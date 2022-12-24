<?php

namespace App\Controller\nest;

use App\Repository\BiblioIRISRepository;
use App\Entity\BiblioIRIS;
use App\form\LibraryFormType;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LibraryController extends AbstractController
{
    #[Route('/library', name: 'app_library')]
    public function index(): Response
    {
        $form = $this->createForm(LibraryFormType::class);
        return $this->renderForm('library/index.html.twig',
            [
                'libraryForm' => $form,
                'user' => $this->getUser()
            ]
        );
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/library/new', name: 'app_library_new')]
    public function new(Request $request,BiblioIRISRepository $biblioIRISRepository): Response
    {
        $form = $this->createForm(LibraryFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data=$form->getData();
            $arrayFiles=explode(',',$data['files']);
            $library = new BiblioIRIS();
            $library->setContent($data['content']);
            $library->setFiles($arrayFiles);
            $library->setPostedBy($this->getUser());
            $biblioIRISRepository->add($library,true);
            return $this->redirectToRoute('app_library');

        }
        return $this->renderForm('library/index.html.twig',
            [
                'libraryForm' => $form,
                'user' => $this->getUser()
            ]
        );

    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/library/delete/{id}', name: 'app_library_delete')]
    public function delete(BiblioIRISRepository $biblioIRISRepository,$id): Response
    {
        $library=$biblioIRISRepository->find($id);
        $biblioIRISRepository->remove($library,true);
        return $this->redirectToRoute('app_library');
    }
}
