<?php

namespace App\Controller\nest;

use App\Entity\Trainer;
use App\form\TrainerFormType;
use App\Repository\TrainerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/trainer')]
class TrainerController extends AbstractController
{
    #[Route('', name: 'app_trainer_index', methods: ['GET'])]
    public function index(TrainerRepository $trainerRepository, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $template = $request->query->get('ajax') ? 'trainer/table.html.twig' : 'trainer/index.html.twig';
        $trainerForm = $this->createForm(TrainerFormType::class);
        return $this->renderForm($template, [
            'trainers' => $trainerRepository->findAll(),
            'trainerForm' => $trainerForm,
        ]);
    }

    #[Route('/new', name: 'app_trainer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, TrainerRepository $trainerRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $trainer = new Trainer();
        $form = $this->createForm(TrainerFormType::class, $trainer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($trainer);
            $entityManager->flush();
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }
            return $this->redirectToRoute('app_trainer_index', [], Response::HTTP_SEE_OTHER);
        }
        $template = $request->isXmlHttpRequest() ? '_modal.html.twig' : 'trainer/index.html.twig';
        return $this->renderForm($template, [
            'trainers' => $trainerRepository->findAll(),
            'trainerForm' => $form,
        ],new Response(null,
            $form->isSubmitted() ? 422 : 200
        ));
    }

    #[Route('/{id}/edit', name: 'app_trainer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trainer $trainer, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(TrainerFormType::class, $trainer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }
            return $this->redirectToRoute('app_trainer_index', [], Response::HTTP_SEE_OTHER);
        }
        $template = $request->query->get('ajax') ? '_modal.edit.html.twig' : 'trainer/index.html.twig';
        return $this->renderForm($template, [
            'form' => $form,
            'modalTitle' => 'Edit Trainer',
            'routeName' => 'app_trainer_edit',
            'extraForm' => null,
            'secondExtraForm' => null,
            'id' => $trainer->getId(),
        ],new Response(null,
            $form->isSubmitted() ? 422 : 200
        ));
    }

    #[Route('/{id}/edit', name: 'app_trainer_update', methods: ['GET', 'POST'])]
    public function update(Request $request, Trainer $trainer): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(TrainerFormType::class, $trainer);
        $template = $request->query->get('ajax') ? '_modal.edit.html.twig' : 'trainer/index.html.twig';
        return $this->renderForm($template, [
            'form' => $form,
            'modalTitle' => 'Edit Trainer',
            'routeName' => 'app_trainer_edit',
            'extraForm' => null,
            'secondExtraForm' => null,
            'id' => $trainer->getId(),
        ]);
    }

    #[Route('/delete/{id}', name: 'app_trainer_delete')]
    public function delete(Request $request, Trainer $trainer, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $entityManager->remove($trainer);
            $entityManager->flush();
        return $this->redirectToRoute('app_trainer_index', [], Response::HTTP_SEE_OTHER);
    }
}
