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
use App\Repository\DepartmentRepository;
use App\Repository\EngagementPostRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Repository\WorkPostRepository;
use App\Repository\WorkshopRepository;
use App\Service\NotificationService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WorkshopController extends AbstractController
{
    #[Route('/workshop', name: 'app_workshop')]
    public function index(DepartmentRepository $departmentRepository,UserRepository $userRepository, PostRepository $postRepository,Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $template = $request->query->get('ajax') ? 'workshops/table.html.twig' : 'workshops/index.html.twig';
        $postForm = $this->createForm(PostFormType::class);
        $engagementPostForm = $this->createForm(EngagementPostFormType::class);
        $user = $userRepository->find($this->getUser()?->getId());
        $roles  = ['ROLE_IT','ROLE_BUSINESS','ROLE_DEVCO','ROLE_MARKETING'];
        if (!in_array($user?->getRoles()[0], $roles, true)) {
            $workshops = $postRepository->findWorkshops();
        }else{
            $workshops = $postRepository->findWorkshopsByDepartments([$user?->getDepartment(),$departmentRepository->findOneBy(['name' => 'All'])]);
        }
        return $this->render($template, [
            'workshops' => $workshops,
            'postForm' => $postForm->createView(),
            'engagementPostForm' => $engagementPostForm->createView(),
        ]);
    }

    #[Route('/workshop/add', name: 'app_workshop_add')]
    public function add(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $postForm = $this->createForm(PostFormType::class);
        $engagementPostForm = $this->createForm(EngagementPostFormType::class);
        return $this->renderForm('workshops/add.html.twig',
            [
                'postFrom' => $postForm,
                'engagementPostForm' => $engagementPostForm
            ]);
    }


    #[Route('/workshop/new', name: 'app_workshop_new')]
    public function new(Request $request, UserRepository $userRepository, PostRepository $postRepository, EngagementPostRepository $engagementPostRepository, WorkshopRepository $workshopRepository, WorkPostRepository $workPostRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $postForm = $this->createForm(PostFormType::class);
        $engagementPostForm = $this->createForm(EngagementPostFormType::class);
        $postForm->handleRequest($request);
        $engagementPostForm->handleRequest($request);
        if ($postForm->isSubmitted() && $postForm->isValid() && $engagementPostForm->isSubmitted() && $engagementPostForm->isValid()) {
            $postData = $postForm->getData();
            $engagementPostData = $engagementPostForm->getData();
            $user = $userRepository->findOneBy(['id' => $this->getUser()?->getId()]);
            $post = new Post();
            $engagementPost = new EngagementPost();
            $workPost = new WorkPost();
            $workshop = new Workshop();
            $post->setName($postData['name']);
            $post->setPublishDate(new \DateTimeImmutable());
            $post->setAuthor($this->getUser());
            if (empty($postData['departments'])) {
                foreach ($postData['departments'] as $department) {
                    $post->addDepartment($department);
                }
            }else{
                $post->addDepartment($user?->getDepartment());
            }
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
            $departments = [];
            foreach ($post->getDepartments() as $department) {
                $departments[] = $department;
            }
            NotificationService::sendNotificationToEagles('New Workshop has been published',$post->getName(), $departments, $userRepository);
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }
            return $this->redirectToRoute('app_workshop');
        }
        $template = $request->isXmlHttpRequest() ? '_modal.html.twig' : 'workshops/index.html.twig';
        return $this->renderForm($template, [
            'postForm' => $postForm->createView(),
            'engagementPostForm' => $engagementPostForm->createView(),
        ],new Response(null,
            $postForm->isSubmitted() ? 422 : 200
        ));
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/workshop/delete/{id}', name: 'app_workshop_delete')]
    public function delete(PostRepository $postRepository,Post $post): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $postRepository->remove($post, true);
        return $this->redirectToRoute('app_workshop');
    }

    #[Route('/workshop/update/{id}', name: 'app_workshop_update')]
    public function edit(Request $request, EngagementPostRepository $engagementPostRepository, PostFormType $postFormType, Post $post, PostRepository $postRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $engagementPost=$engagementPostRepository->findOneBy(['post'=>$post]);
        $postForm = $this->createForm(PostFormType::class, $post);
        $engagementPostForm = $this->createForm(EngagementPostFormType::class, $engagementPost);
        $template = $request->query->get('ajax') ? '_modal.edit.html.twig' : 'workshops/index.html.twig';
        return $this->renderForm($template,
            [
                'form' => $postForm,
                'extraForm' => $engagementPostForm,
                'secondExtraForm' => null,
                'modalTitle' => 'Edit Workshop',
                'routeName' => 'app_workshop_update_submit',
                'id' => $post->getId(),
                'post'=>$post
            ]);
    }

    #[Route('/workshop/update/submit/{id}', name: 'app_workshop_update_submit')]
    public function update(Request $request, EngagementPostRepository $engagementPostRepository, PostFormType $postFormType, Post $post, PostRepository $postRepository, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $engagementPost=$engagementPostRepository->findOneBy(['post'=>$post]);
        $postForm = $this->createForm(PostFormType::class, $post);
        $engagementPostForm = $this->createForm(EngagementPostFormType::class, $engagementPost);
        $postForm->handleRequest($request);
        $engagementPostForm->handleRequest($request);
        if ($postForm->isSubmitted() && $postForm->isValid() && $engagementPostForm->isSubmitted() && $engagementPostForm->isValid()) {
            $postData = $postForm->getData();
            $engagementPostData = $engagementPostForm->getData();
            $post->setName($postData->getName());
            $roles  = ['ROLE_IT','ROLE_BUSINESS','ROLE_DEVCO','ROLE_MARKETING'];
            foreach ($roles as $role){
                if (!in_array($role, $this->getUser()?->getRoles(), true)) {
                    foreach ($postData->getDepartments() as $department) {
                        $post->addDepartment($department);
                    }
                }else{
                    $post->addDepartment($this->getUser()?->getDepartment());
                }
            }
            $postRepository->add($post);
            $engagementPost?->setPlace($engagementPostData->getPlace());
            $engagementPost?->setLink($engagementPostData->getLink());
            $engagementPost?->setDate($engagementPostData->getDate());
            $engagementPost?->setStart($engagementPostData->getStart());
            $engagementPost?->setEnd($engagementPostData->getEnd());
            $engagementPostRepository->add($engagementPost);
            $departments = [];
            foreach ($post->getDepartments() as $department) {
                $departments[] = $department;
            }
            NotificationService::sendNotificationToEagles('Workshop has been updated',$post->getName(), $departments, $userRepository);
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }
            return $this->redirectToRoute('app_workshop');
        }
        $template = $request->isXmlHttpRequest() ? '_modal.edit.html.twig' : 'workshops/index.html.twig';
        return $this->renderForm($template,
            [
                'form' => $postForm,
                'extraForm' => $engagementPostForm,
                'secondExtraForm' => null,
                'modalTitle' => 'Edit Workshop',
                'routeName' => 'app_workshop_update_submit',
                'post'=>$post

            ],new Response(null,
            $postForm->isSubmitted() ? 422 : 200
        ));
    }
}



