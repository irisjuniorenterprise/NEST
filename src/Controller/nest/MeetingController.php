<?php

namespace App\Controller\nest;

use App\Entity\Meeting;
use App\form\MeetingFormType;
use App\Repository\DepartmentRepository;
use App\Repository\MeetingRepository;
use App\Repository\UserRepository;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\EngagementPost;
use App\Entity\Post;
use App\Entity\WorkPost;

use App\form\EngagementPostFormType;

use App\form\PostFormType;

use App\Repository\EngagementPostRepository;
use App\Repository\PostRepository;

use App\Repository\WorkPostRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MeetingController extends AbstractController
{
    #[Route('/meeting', name: 'app_meeting')]
    public function index(Request $request, UserRepository $userRepository, PostRepository $postRepository, DepartmentRepository $departmentRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $template = $request->query->get('ajax') ? 'meetings/table.html.twig' : 'meetings/index.html.twig';
        $meetingForm = $this->createForm(MeetingFormType::class);
        $postForm = $this->createForm(PostFormType::class);
        $engagementPostForm = $this->createForm(EngagementPostFormType::class);
        $user = $userRepository->find($this->getUser()?->getId());
        $roles = ['ROLE_IT', 'ROLE_BUSINESS', 'ROLE_DEVCO', 'ROLE_MARKETING'];
        if (!in_array($user?->getRoles()[0], $roles, true)) {
            $meetings = $postRepository->findMeetings();
        } else {
            $meetings = $postRepository->findMeetingsByDepartments([$user?->getDepartment(), $departmentRepository->findOneBy(['name' => 'All'])]);
        }
        return $this->renderForm($template, [
            'meetings' => $meetings,
            'meetingForm' => $meetingForm,
            'postForm' => $postForm,
            'engagementPostForm' => $engagementPostForm,
        ]);
    }

    #[Route('/meeting/add', name: 'app_meeting_add')]
    public function add(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $postForm = $this->createForm(PostFormType::class);
        $engagementPostForm = $this->createForm(EngagementPostFormType::class);
        $meetingForm = $this->createForm(MeetingFormType::class);
        return $this->renderForm('meetings/add.html.twig',
            [
                'postForm' => $postForm,
                'engagementPostForm' => $engagementPostForm,
                'meetingForm' => $meetingForm
            ]);
    }

    #[Route('/meeting/new', name: 'app_meeting_new')]
    public function new(Request $request, UserRepository $userRepository, PostRepository $postRepository, EngagementPostRepository $engagementPostRepository, MeetingRepository $meetingRepository, WorkPostRepository $workPostRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $postForm = $this->createForm(PostFormType::class);
        $engagementPostForm = $this->createForm(EngagementPostFormType::class);
        $meetingForm = $this->createForm(MeetingFormType::class);
        $postForm->handleRequest($request);
        $engagementPostForm->handleRequest($request);
        $meetingForm->handleRequest($request);

        if ($postForm->isSubmitted() && $postForm->isValid() && $engagementPostForm->isSubmitted() && $engagementPostForm->isValid()) {
            $postData = $postForm->getData();
            $engagementPostData = $engagementPostForm->getData();
            $meetingData = $meetingForm->getData();
            $user = $userRepository->findOneBy(['id' => $this->getUser()?->getId()]);
            $post = new Post();
            $engagementPost = new EngagementPost();
            $workPost = new WorkPost();
            $meeting = new Meeting();
            $post->setName($postData['name']);
            $post->setPublishDate(new \DateTimeImmutable());
            $post->setAuthor($this->getUser());
            if ($postData['departments'] !== null) {
                foreach ($postData['departments'] as $department) {
                    $post->addDepartment($department);
                }
            } else {
                $post->addDepartment($user?->getDepartment());
            }
            $postRepository->add($post);
            $engagementPost->setPlace($engagementPostData['place']);
            $engagementPost->setLink($engagementPostData['link']);
            $engagementPost->setDate(new \DateTimeImmutable());
            $engagementPost->setStart($engagementPostData['start']);
            $engagementPost->setEnd($engagementPostData['end']);
            $engagementPost->setPost($post);
            $engagementPostRepository->add($engagementPost);
            $workPost->setEngagementPost($engagementPost);
            $workPostRepository->add($workPost);
            $meeting->setType($meetingData['type']);
            $meeting->setWorkPost($workPost);
            $meetingRepository->add($meeting);
            $departments = [];
            foreach ($post->getDepartments() as $department) {
                $departments[] = $department;
            }
            NotificationService::sendNotificationToEagles('New Meeting has been published', $post->getName(), $departments, $userRepository);
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }
            return $this->redirectToRoute('app_meeting');
        }
        $template = $request->isXmlHttpRequest() ? '_modal.html.twig' : 'meetings/index.html.twig';
        return $this->renderForm($template, [
            'postForm' => $postForm->createView(),
            'engagementPostForm' => $engagementPostForm->createView(),
            'meetingForm' => $meetingForm->createView(),
        ], new Response(null,
            $postForm->isSubmitted() ? 422 : 200
        ));
    }

    #[Route('/meeting/delete/{id}', name: 'app_meeting_delete')]
    public function delete(Post $post, PostRepository $postRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $postRepository->remove($post, true);
        return $this->redirectToRoute('app_meeting');
    }

    #[Route('/meeting/update/{id}', name: 'app_meeting_update')]
    public function edit(Request $request, Post $post, EngagementPostRepository $engagementPostRepository, MeetingRepository $meetingRepository, WorkPostRepository $workPostRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $postForm = $this->createForm(PostFormType::class, $post);
        $engagementPost = $engagementPostRepository->findOneBy(['post' => $post]);
        $engagementPostForm = $this->createForm(EngagementPostFormType::class, $engagementPost);
        $workPost = $workPostRepository->findOneBy(['engagementPost' => $engagementPost]);
        $meeting = $meetingRepository->findOneBy(['workPost' => $workPost]);
        $meetingForm = $this->createForm(MeetingFormType::class, $meeting);
        $template = $request->query->get('ajax') ? '_modal.edit.html.twig' : 'meetings/index.html.twig';
        return $this->renderForm($template,
            [
                'form' => $postForm,
                'extraForm' => $engagementPostForm,
                'secondExtraForm' => $meetingForm,
                'modalTitle' => 'Edit Meeting',
                'routeName' => 'app_meeting_update_submit',
                'id' => $post->getId(),
                'post' => $post
            ]);
    }

    #[Route('/meeting/update/submit/{id}', name: 'app_meeting_update_submit')]
    public function update(Request $request, PostRepository $postRepository, EngagementPostRepository $engagementPostRepository, MeetingRepository $meetingRepository, WorkPostRepository $workPostRepository, Post $post, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $postForm = $this->createForm(PostFormType::class, $post);
        $engagementPost = $engagementPostRepository->findOneBy(['post' => $post]);
        $engagementPostForm = $this->createForm(EngagementPostFormType::class, $engagementPost);
        $workPost = $workPostRepository->findOneBy(['engagementPost' => $engagementPost]);
        $meeting = $meetingRepository->findOneBy(['workPost' => $workPost]);
        $meetingForm = $this->createForm(MeetingFormType::class, $meeting);
        $postForm->handleRequest($request);
        $engagementPostForm->handleRequest($request);
        $meetingForm->handleRequest($request);

        if ($postForm->isSubmitted() && $postForm->isValid() && $engagementPostForm->isSubmitted() && $engagementPostForm->isValid()) {
            $postData = $postForm->getData();
            $engagementPostData = $engagementPostForm->getData();
            $meetingData = $meetingForm->getData();
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
            $postRepository->add($post);
            $engagementPost?->setPlace($engagementPostData->getPlace());
            $engagementPost?->setLink($engagementPostData->getLink());
            $engagementPost?->setStart($engagementPostData->getStart());
            $engagementPost?->setEnd($engagementPostData->getEnd());
            $engagementPost?->setPost($post);
            $engagementPostRepository->add($engagementPost);
            $workPost?->setEngagementPost($engagementPost);
            $workPostRepository->add($workPost);
            $meeting?->setType($meetingData->getType());
            $meetingRepository->add($meeting);
            $departments = [];
            foreach ($post->getDepartments() as $department) {
                $departments[] = $department;
            }
            NotificationService::sendNotificationToEagles('Meeting has been updated', $post->getName(), $departments, $userRepository);
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }
            return $this->redirectToRoute('app_meeting');
        }
        $template = $request->isXmlHttpRequest() ? '_modal.edit.html.twig' : 'meetings/index.html.twig';
        return $this->renderForm($template,
            [
                'form' => $postForm,
                'extraForm' => $engagementPostForm,
                'secondExtraForm' => $meetingForm,
                'modalTitle' => 'Edit Meeting',
                'routeName' => 'app_meeting_update_submit',
                'post' => $post

            ], new Response(null,
                $postForm->isSubmitted() ? 422 : 200
            ));
    }
}
