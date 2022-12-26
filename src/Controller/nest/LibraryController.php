<?php

namespace App\Controller\nest;

use App\Entity\Article;
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
    public function index(BiblioIRISRepository $biblioIRISRepository): Response
    {
        $form = $this->createForm(LibraryFormType::class);
        return $this->renderForm('library/index.html.twig',
            [
                'libraryForm' => $form,
                'user' => $this->getUser(),
                'resources' => $biblioIRISRepository->findAll(),
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
    #[Route('/library/update/{id}', name: 'app_library_update')]
    public function update(Request $request,BiblioIRISRepository $biblioIRISRepository, BiblioIRIS $biblioIRIS): Response
    {
        $form = $this->createForm(LibraryFormType::class,$biblioIRIS);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $biblioIRISRepository->persist($biblioIRIS);
            $biblioIRISRepository->flush();
            $this->addFlash('success','Article updated');

            return $this->redirectToRoute('app_library',['id'=>$biblioIRIS->getId()]);
        }
        return $this->renderForm('library/index.html.twig',
            [
                'libraryForm' => $form,
                'user' => $this->getUser()
            ]
        );

    }
}
