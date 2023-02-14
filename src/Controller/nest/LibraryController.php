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
    public function index(BiblioIRISRepository $biblioIRISRepository, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $template = $request->query->get('ajax') ? 'library/_table.html.twig' : 'library/index.html.twig';
        $form = $this->createForm(LibraryFormType::class);
        return $this->renderForm($template,
            [
                'libraryForm' => $form,
                'user' => $this->getUser(),
                'resources' => $biblioIRISRepository->findBy([], ['id' => 'DESC']),
            ]
        );
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/library/new', name: 'app_library_new')]
    public function new(Request $request, BiblioIRISRepository $biblioIRISRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(LibraryFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $links = $_POST['links'];
            $data = $form->getData();
            $library = new BiblioIRIS();
            $library->setContent($data['content']);
            $library->setFiles($links);
            $library->setPostedBy($this->getUser());
            $biblioIRISRepository->add($library, true);
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
    public function delete(BiblioIRISRepository $biblioIRISRepository, $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $library = $biblioIRISRepository->find($id);
        $biblioIRISRepository->remove($library, true);
        return $this->redirectToRoute('app_library');
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/library/update/submit/{id}', name: 'app_library_update_submit')]
    public function update(Request $request, BiblioIRISRepository $biblioIRISRepository, BiblioIRIS $biblioIRIS): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(LibraryFormType::class, $biblioIRIS);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $links = $_POST['links'];
            $biblioIRIS->setFiles($links);
            $biblioIRISRepository->add($biblioIRIS, true);
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }
            return $this->redirectToRoute('app_library');
        }
        $template = $request->isXmlHttpRequest() ? '_modal.edit.html.twig' : 'library/index.html.twig';
        return $this->renderForm($template,
            [
                'form' => $form,
                'modalTitle' => 'Edit resource',
                'routeName' => 'app_library',
                'extraForm' => null,
            ], new Response(null, $form->isSubmitted() ? 422 : 200));

    }

    #[Route('/library/update/{id}', name: 'app_library_update')]
    public function edit(BiblioIRIS $biblioIRIS, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(LibraryFormType::class, $biblioIRIS);
        $template = $request->query->get('ajax') ? '_modal.edit.html.twig' : 'library/index.html.twig';
        return $this->renderForm($template,
            [
                'form' => $form,
                'modalTitle' => 'Edit resource',
                'routeName' => 'app_library_update_submit',
                'extraForm' => null,
                'secondExtraForm' => null,
                'id' => $biblioIRIS->getId(),
                'links' => $biblioIRIS->getFiles(),
            ]
        );
    }
}
