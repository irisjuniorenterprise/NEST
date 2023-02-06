<?php

namespace App\Controller\nest;

use App\Entity\BiblioIRIS;
use App\Entity\EngagementPost;
use App\Entity\Post;
use App\Entity\WorkPost;
use App\Entity\Workshop;
use App\form\EngagementPostFormType;
use App\form\LibraryFormType;
use App\form\PostFormType;
use App\Repository\BiblioIRISRepository;
use App\Repository\BlameRepository;
use App\Repository\EngagementPostRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Repository\WorkPostRepository;
use App\Repository\WorkshopRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WorkshopController extends AbstractController
{
    #[Route('/workshops', name: 'app_workshops')]
    public function index(WorkshopRepository $workshopRepository): Response
    {
        return $this->render('workshops/index.html.twig', [
            'workshops' => $workshopRepository->findAll(),
        ]);
    }

    #[Route('/workshop/add', name: 'app_workshop_add')]
    public function add():Response
    {
        $postForm = $this->createForm(PostFormType::class);
        $engagementPostForm = $this->createForm(EngagementPostFormType::class);
        return $this->renderForm('workshops/edit.html.twig',
        [
            'postFrom'=>$postForm,
            'engagementPosForm'=>$engagementPostForm
        ]);
    }


    #[Route('/workshop/new', name: 'app_workshop_new')]
    public function new(Request $request, PostRepository $postRepository ,EngagementPostRepository $engagementPostRepository, WorkshopRepository $workshopRepository , WorkPostRepository $workPostRepository): Response
    {
        $postForm = $this->createForm(PostFormType::class);
        $engagementPostForm = $this->createForm(EngagementPostFormType::class);
        $postForm->handleRequest($request);
        $engagementPostForm->handleRequest($request);

        if ($postForm->isSubmitted() && $postForm->isValid() && $engagementPostForm->isSubmitted() && $engagementPostForm->isValid()) {
            $postData = $postForm->getData();
            $engagementPostData = $engagementPostForm->getData();
            $post = new Post();
            $engagementPost= new EngagementPost();
            $workPost= new WorkPost();
            $workshop = new Workshop();
            $post->setName($postData['name']);
            $post->setPublishDate(new \DateTimeImmutable());
            $post->setAuthor($this->getUser());
            $postRepository->add($post, true);


            $engagementPost->setPlace($engagementPostData['place']);
            $engagementPost->setLink($engagementPostData['link']);
            $engagementPost->setDate(new \DateTimeImmutable());
            $engagementPost->setStart($engagementPostData['start']);
            $engagementPost->setEnd($engagementPostData['end']);
            $engagementPost->setPost($post);
            $engagementPostRepository->add($engagementPost);

            $workPost->setEngagementPost($engagementPost);
            $workPostRepository->add($workPost);


            $workshop->setWorkPost($workPost);
            $workshopRepository->add($workshop);



            return $this->redirectToRoute('app_workshops');

        }


        return $this->redirectToRoute('app_workshops');
    }
}
