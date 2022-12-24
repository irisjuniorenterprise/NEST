<?php

namespace App\Controller\nest;

use App\Entity\Blame;
use App\Form\BlameFormType;
use App\Repository\BlameRepository;
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
    public function new(BlameRepository $blameRepository, Request $request)
    {
        $form = $this->createForm(BlameFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $blame =new blame();
            $blame->setEagle($this->getUser());
            $blame->setDate(new \DateTime());
            $blame->setReason($data['reason']);
            $blameRepository->add($blame,true);
            return $this->redirectToRoute('app_blame');


        }
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

