<?php

namespace App\Controller\nest;

use App\Entity\EngagementPost;
use App\Entity\Post;
use App\Entity\Trainer;
use App\Entity\Training;
use App\form\EngagementPostFormType;
use App\form\PostFormType;
use App\form\TrainingFormType;
use App\Repository\EngagementPostRepository;
use App\Repository\PostRepository;
use App\Repository\TrainerRepository;
use App\Repository\TrainingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrainingController extends AbstractController
{
    #[Route('/training', name: 'app_training')]
    public function trainingIndex(TrainingRepository $trainingRepository): Response
    {
        return $this->render('trainings/index.html.twig',[
            'trainings'=>$trainingRepository->findAll(),
        ]);
    }

    #[Route('/training/add', name: 'app_training_add')]
    public function addTrainingForm(TrainerRepository $trainerRepository):Response
    {
        $postForm = $this->createForm(PostFormType::class);
        $engagementPostForm = $this->createForm(EngagementPostFormType::class);
        $trainingForm =$this->createForm(TrainingFormType::class);
        return $this->renderForm('trainings/add.html.twig',
            [
                'postFrom'=>$postForm,
                'engagementPosForm'=>$engagementPostForm,
                'trainingForm'=>$trainingForm,
                'trainers'=>$trainerRepository->findAll(),
            ]);
    }

    #[Route('/training/new', name: 'app_training_new')]
    public function submitNewTraining(Request $request, PostRepository $postRepository ,EngagementPostRepository $engagementPostRepository, TrainerRepository $trainerRepository ,TrainingRepository $trainingRepository): Response
    {
        $postForm = $this->createForm(PostFormType::class);
        $engagementPostForm = $this->createForm(EngagementPostFormType::class);

        $postForm->handleRequest($request);
        $engagementPostForm->handleRequest($request);

        if ($postForm->isSubmitted() && $postForm->isValid() && $engagementPostForm->isSubmitted() && $engagementPostForm->isValid()) {
            $postData = $postForm->getData();
            $engagementPostData = $engagementPostForm->getData();
            $trainers=$_POST['trainers'];
            foreach ($_POST['trainers'] as $trainer)
            {
                $trainersGot[]=$trainerRepository->find($trainer);
            }

            $post = new Post();
            $engagementPost= new EngagementPost();
            $training= new Training();

            $post->setName($postData['name']);
            $post->setPublishDate(new \DateTimeImmutable());
            $post->setAuthor($this->getUser());
            $postRepository->add($post,true);


            $engagementPost->setPlace($engagementPostData['place']);
            $engagementPost->setLink($engagementPostData['link']);
            $engagementPost->setDate(new \DateTimeImmutable());
            $engagementPost->setStart($engagementPostData['start']);
            $engagementPost->setEnd($engagementPostData['end']);
            $engagementPost->setPost($post);
            $engagementPostRepository->add($engagementPost);


            $training->setEngagementPost($engagementPost);
            foreach ($trainersGot as $trainer)
            {
                $training->addTrainer($trainer);
            }


            $trainingRepository->add($training);


            return $this->redirectToRoute('app_training');

        }


        return $this->redirectToRoute('app_training');
    }

    #[Route('/training/delete/{id}', name: 'app_training_delete')]
    public function deleteTraining(PostRepository $postRepository, Post $post): Response
    {

        $postRepository->remove($post,true);
        return $this->redirectToRoute('app_training');
    }

    #[Route('/training/modify/{id}', name: 'app_training_modify')]
    public function modifyTrainingForm(TrainerRepository $trainerRepository, Post $post , EngagementPostRepository $engagementPostRepository , TrainingRepository $trainingRepository):Response
    {
        $engagementPost= $engagementPostRepository->findOneBy(['post'=>$post]);
        $postForm = $this->createForm(PostFormType::class ,$post);
        $engagementPostForm = $this->createForm(EngagementPostFormType::class, $engagementPost);
        $training= $trainingRepository->findOneBy(['engagementPost'=>$engagementPost]);
        $trainingForm =$this->createForm(TrainingFormType::class , $training);
        return $this->renderForm('trainings/update.html.twig',
            [
                'postFrom'=>$postForm,
                'engagementPosForm'=>$engagementPostForm,
                'trainingForm'=>$trainingForm,
                'trainers'=>$trainerRepository->findAll(),
                'training'=>$training,
            ]);
    }

    #[Route('/training/update/{id}', name: 'app_training_update')]
    public function updateTraining(Request $request ,Post $post,TrainerRepository $trainerRepository, EngagementPostRepository $engagementPostRepository, PostRepository $postRepository, TrainingRepository $trainingRepository ): Response
    {
        $postForm = $this->createForm(PostFormType::class , $post);
        $engagementPost= $engagementPostRepository->findOneBy(['post'=>$post] );
        $engagementPostForm = $this->createForm(EngagementPostFormType::class , $engagementPost);


        $postForm->handleRequest($request);
        $engagementPostForm->handleRequest($request);
        $training=$trainingRepository->findOneBy(['engagementPost'=>$engagementPost]);


        if ($postForm->isSubmitted() && $postForm->isValid() && $engagementPostForm->isSubmitted() && $engagementPostForm->isValid()) {
            $postData = $postForm->getData();
            $engagementPostData = $engagementPostForm->getData();
            $trainers=$_POST['trainers'];
            foreach ($_POST['trainers'] as $trainer)
            {
                $trainersGot[]=$trainerRepository->find($trainer);
            }



            $post->setName($postData->getName());
            $postRepository->add($post,true);


            $engagementPost->setPlace($engagementPostData->getPlace());
            $engagementPost->setLink($engagementPostData->getLink());
            $engagementPost->setStart($engagementPostData->getStart());
            $engagementPost->setEnd($engagementPostData->getEnd());
            $engagementPost->setPost($post);
            $engagementPostRepository->add($engagementPost);


            $training->setEngagementPost($engagementPost);
            foreach ($trainersGot as $trainer)
            {
                $training->addTrainer($trainer);
            }


            $trainingRepository->add($training);


            return $this->redirectToRoute('app_training');

        }


        return $this->redirectToRoute('app_training');
    }



}
