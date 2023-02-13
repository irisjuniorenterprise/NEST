<?php

namespace App\Controller\nest;

use App\Entity\EngagementPost;
use App\Entity\Post;
use App\Entity\Trainer;
use App\Entity\Training;
use App\form\EngagementPostFormType;
use App\form\PostFormType;
use App\form\TrainingFormType;
use App\Repository\DepartmentRepository;
use App\Repository\EngagementPostRepository;
use App\Repository\PostRepository;
use App\Repository\TrainerRepository;
use App\Repository\TrainingRepository;
use App\Repository\UserRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrainingController extends AbstractController
{
    #[Route('/training', name: 'app_training')]
    public function trainingIndex(Request $request, UserRepository $userRepository, PostRepository $postRepository, DepartmentRepository $departmentRepository): Response
    {
        $template = $request->query->get('ajax') ? 'trainings/table.html.twig' : 'trainings/index.html.twig';
        $postForm = $this->createForm(PostFormType::class);
        $engagementPostForm = $this->createForm(EngagementPostFormType::class);
        $trainingForm =$this->createForm(TrainingFormType::class);
        $user = $userRepository->find($this->getUser()?->getId());
        $roles = ['ROLE_IT', 'ROLE_BUSINESS', 'ROLE_DEVCO', 'ROLE_MARKETING'];
        if (!in_array($user?->getRoles()[0], $roles, true)) {
            $trainings = $postRepository->findTrainings();
        } else {
            $trainings = $postRepository->findTrainingsByDepartments([$user?->getDepartment(), $departmentRepository->findOneBy(['name' => 'All'])]);
        }
        return $this->renderForm($template, [
            'trainings' => $trainings,
            'trainingForm' => $trainingForm,
            'postForm' => $postForm,
            'engagementPostForm' => $engagementPostForm,
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
            ]);
    }

    #[Route('/training/new', name: 'app_training_new')]
    public function submitNewTraining(Request $request, PostRepository $postRepository , UserRepository $userRepository, EngagementPostRepository $engagementPostRepository, TrainerRepository $trainerRepository ,TrainingRepository $trainingRepository): Response
    {
        $postForm = $this->createForm(PostFormType::class);
        $engagementPostForm = $this->createForm(EngagementPostFormType::class);
        $trainingForm =$this->createForm(TrainingFormType::class);
        $postForm->handleRequest($request);
        $engagementPostForm->handleRequest($request);
        if ($postForm->isSubmitted() && $postForm->isValid() && $engagementPostForm->isSubmitted() && $engagementPostForm->isValid()) {
            $postData = $postForm->getData();
            $engagementPostData = $engagementPostForm->getData();
            $user = $userRepository->findOneBy(['id' => $this->getUser()?->getId()]);
            $trainers=$_POST['training_form']['trainers'];
            foreach ($trainers as $trainer)
            {
                $trainersGot[]=$trainerRepository->find($trainer);
            }
            $post = new Post();
            $engagementPost= new EngagementPost();
            $training= new Training();
            $post->setName($postData['name']);
            $post->setPublishDate(new \DateTimeImmutable());
            if ($postData['departments'] !== null) {
                foreach ($postData['departments'] as $department) {
                    $post->addDepartment($department);
                }
            } else {
                $post->addDepartment($user?->getDepartment());
            }
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
            $departments = [];
            foreach ($post->getDepartments() as $department) {
                $departments[] = $department;
            }
            NotificationService::sendNotificationToEagles('New Trainings has been published', $post->getName(), $departments,$userRepository);
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }
            return $this->redirectToRoute('app_training');
        }
        $template = $request->isXmlHttpRequest() ? '_modal.html.twig' : 'trainings/index.html.twig';
        return $this->renderForm($template, [
            'postForm' => $postForm->createView(),
            'engagementPostForm' => $engagementPostForm->createView(),
            'trainingForm' => $trainingForm->createView(),
        ], new Response(null,
            $postForm->isSubmitted() ? 422 : 200
        ));
    }

    #[Route('/training/delete/{id}', name: 'app_training_delete')]
    public function deleteTraining(PostRepository $postRepository, Post $post): Response
    {
        $postRepository->remove($post,true);
        return $this->redirectToRoute('app_training');
    }

    #[Route('/training/modify/{id}', name: 'app_training_modify')]
    public function modifyTrainingForm(TrainerRepository $trainerRepository, Request $request,Post $post , EngagementPostRepository $engagementPostRepository , TrainingRepository $trainingRepository):Response
    {
        $engagementPost= $engagementPostRepository->findOneBy(['post'=>$post]);
        $postForm = $this->createForm(PostFormType::class ,$post);
        $engagementPostForm = $this->createForm(EngagementPostFormType::class, $engagementPost);
        $training= $trainingRepository->findOneBy(['engagementPost'=>$engagementPost]);
        $trainingForm =$this->createForm(TrainingFormType::class , $training);
        $template = $request->query->get('ajax') ? '_modal.edit.html.twig' : 'trainings/index.html.twig';
        return $this->renderForm($template,
            [
                'form' => $postForm,
                'extraForm' => $engagementPostForm,
                'secondExtraForm' => $trainingForm,
                'modalTitle' => 'Edit Training',
                'routeName' => 'app_training_update',
                'id' => $post->getId(),
                'post' => $post
            ]);
    }

    #[Route('/training/update/{id}', name: 'app_training_update')]
    public function updateTraining(Request $request ,Post $post,TrainerRepository $trainerRepository, UserRepository $userRepository,EngagementPostRepository $engagementPostRepository, PostRepository $postRepository, TrainingRepository $trainingRepository ): Response
    {
        $postForm = $this->createForm(PostFormType::class , $post);
        $engagementPost= $engagementPostRepository->findOneBy(['post'=>$post] );
        $engagementPostForm = $this->createForm(EngagementPostFormType::class , $engagementPost);
        $trainingForm =$this->createForm(TrainingFormType::class);
        $postForm->handleRequest($request);
        $engagementPostForm->handleRequest($request);
        $training=$trainingRepository->findOneBy(['engagementPost'=>$engagementPost]);
        if ($postForm->isSubmitted() && $postForm->isValid() && $engagementPostForm->isSubmitted() && $engagementPostForm->isValid()) {
            $postData = $postForm->getData();
            $engagementPostData = $engagementPostForm->getData();
            $trainers=$_POST['training_form']['trainers'];
            foreach ($trainers as $trainer)
            {
                $trainersGot[]=$trainerRepository->find($trainer);
            }
            $post->setName($postData->getName());
            $roles = ['ROLE_IT', 'ROLE_BUSINESS', 'ROLE_DEVCO', 'ROLE_MARKETING'];
            foreach ($roles as $role) {
                if (!in_array($role, $this->getUser()?->getRoles(), true)) {
                    foreach ($postData->getDepartments() as $department) {
                        $post->addDepartment($department);
                    }
                } else {
                    $post->addDepartment($this->getUser()?->getDepartment());
                }
            }
            $postRepository->add($post,true);
            $engagementPost?->setPlace($engagementPostData->getPlace());
            $engagementPost?->setLink($engagementPostData->getLink());
            $engagementPost?->setStart($engagementPostData->getStart());
            $engagementPost?->setEnd($engagementPostData->getEnd());
            $engagementPost?->setPost($post);
            $engagementPostRepository->add($engagementPost);
            $training?->setEngagementPost($engagementPost);
            foreach ($trainersGot as $trainer)
            {
                $training?->addTrainer($trainer);
            }
            $trainingRepository->add($training);
            $departments = [];
            foreach ($post->getDepartments() as $department) {
                $departments[] = $department;
            }
            NotificationService::sendNotificationToEagles('Training has been updated', $post->getName(), $departments, $userRepository);
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }
            return $this->redirectToRoute('app_training');
        }
        $template = $request->isXmlHttpRequest() ? '_modal.edit.html.twig' : 'trainings/index.html.twig';
        return $this->renderForm($template,
            [
                'form' => $postForm,
                'extraForm' => $engagementPostForm,
                'secondExtraForm' => $trainingForm,
                'modalTitle' => 'Edit Training',
                'routeName' => 'app_workshop_update_submit',
                'post' => $post

            ], new Response(null,
                $postForm->isSubmitted() ? 422 : 200
            ));
    }
}
