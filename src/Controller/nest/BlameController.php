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
    public function index(BlameRepository $blameRepository): Response
    {
        $form = $this->createForm(BlameFormType::class);
        return $this->renderForm('blame/index.html.twig', [
            'blames' => $blameRepository->findAll(),
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
            return $this->redirectToRoute('app_blame');
        }
        return $this->renderForm('blame/index.html.twig', [
            'blames' => $blameRepository->findAll(),
            'blameForm' => $form,
        ]);
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
        return $this->redirectToRoute('app_blame',[
            'blame_deleted'=> true ,
            'blame_added'=> false ,
        ]);
    }

}

