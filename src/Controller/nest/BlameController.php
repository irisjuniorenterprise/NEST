<?php

namespace App\Controller\nest;

use App\Entity\Blame;

use App\form\BlameFormType;
use App\Repository\BlameRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlameController extends AbstractController
{
    #[Route('/blame', name: 'app_blame')]
    public function index(BlameRepository $blameRepository, Request $request): Response
    {
        $template = $request->query->get('ajax') ? 'partials/_table.html.twig' : 'blame/index.html.twig';
        $form = $this->createForm(BlameFormType::class);
        return $this->renderForm($template, [
            'blames' => $blameRepository->findBy([], ['id' => 'DESC']),
            'blameForm' => $form,
        ]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/blame/new', name: 'app_blame_new')]
    public function new(BlameRepository $blameRepository, Request $request, UserRepository $userRepository):Response
    {
        $form = $this->createForm(BlameFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $eagleBlamed = $userRepository->find($data['eagle']);
            $blame =new blame();
            $blame->setEagle($eagleBlamed);
            $blame->setDate(new \DateTime());
            $blame->setReason($data['reason']);
            $blameRepository->add($blame,true);
            $this->addFlash('success', 'Blame added');
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }
            return $this->redirectToRoute('app_blame');
        }
        $template = $request->isXmlHttpRequest() ? '_modal.html.twig' : 'blame/index.html.twig';
        return $this->renderForm($template, [
            'blames' => $blameRepository->findAll(),
            'blameForm' => $form,
        ],new Response(null,
            $form->isSubmitted() ? 422 : 200
        ));
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/blame/delete/{id}', name: 'app_blame_delete')]
    public function delete(BlameRepository $blameRepository, $id): Response
    {
        $blame = $blameRepository->find($id);
        $blameRepository->remove($blame,true);
        return $this->redirectToRoute('app_blame');
    }

    #[Route('/blame/update/{id}', name: 'app_blame_update')]
    public function edit(Blame $blame, Request $request): Response
    {
        $form = $this->createForm(BlameFormType::class, $blame);
        $template = $request->query->get('ajax') ? '_modal.edit.html.twig' : 'blame/index.html.twig';
        return $this->renderForm($template, [
            'form' => $form,
            'modalTitle' => 'Edit blame',
            'routeName' => 'app_blame_update_submit',
            'id' => $blame->getId(),
        ]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/blame/update/submit/{id}', name: 'app_blame_update_submit', methods: ['POST'])]
    public function update(Blame $blame, Request $request, BlameRepository $blameRepository): Response
    {
        $form = $this->createForm(BlameFormType::class, $blame);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $blameRepository->add($blame,true);
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }
            return $this->redirectToRoute('app_blame');
        }
        $template = $request->isXmlHttpRequest() ? '_modal.edit.html.twig' : 'blame/index.html.twig';
        return $this->renderForm($template, [
            'form' => $form,
            'modalTitle' => 'Edit blame',
            'routeName' => 'app_blame',
        ],new Response(null,
            $form->isSubmitted() ? 422 : 200
        ));
    }

    #[Route('/repeat', name: 'app_repeat')]
    public function show(): Response
    {
        dd($_POST['items']);
    }

}

