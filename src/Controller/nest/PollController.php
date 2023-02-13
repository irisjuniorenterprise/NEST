<?php

namespace App\Controller\nest;

use App\Entity\Announcement;
use App\Entity\Poll;
use App\Entity\PollOption;
use App\Entity\Post;
use App\form\AnnouncementFormType;
use App\form\PollFormType;
use App\form\PollOptionFormType;
use App\form\PostFormType;
use App\Repository\AnnouncementRepository;
use App\Repository\DepartmentRepository;
use App\Repository\PollOptionRepository;
use App\Repository\PollRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Service\NotificationService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PollController extends AbstractController
{
    #[Route('/poll', name: 'app_poll')]
    public function index(PostRepository $postRepository, Request $request, UserRepository $userRepository, DepartmentRepository $departmentRepository): Response
    {
        $template = $request->query->get('ajax') ? 'polls/table.html.twig' : 'polls/index.html.twig';
        $pollForm = $this->createForm(PollFormType::class);
        $postForm = $this->createForm(PostFormType::class);
        $pollOptionForm=$this->createForm(PollOptionFormType::class);
        $user = $userRepository->find($this->getUser()?->getId());
        $roles = ['ROLE_IT', 'ROLE_BUSINESS', 'ROLE_DEVCO', 'ROLE_MARKETING'];
        if (!in_array($user?->getRoles()[0], $roles, true)) {
            $polls = $postRepository->findPolls();
        } else {
            $polls = $postRepository->findPollsByDepartments([$user?->getDepartment(), $departmentRepository->findOneBy(['name' => 'All'])]);
        }
        return $this->renderForm($template, [
            'polls' => $polls,
            'pollForm' => $pollForm,
            'postForm' => $postForm,
            'pollOptionForm' => $pollOptionForm,
        ]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/poll/delete/{id}', name: 'app_poll_delete')]
    public function delete(PostRepository $postRepository, $id ): Response
    {
        $postRepository->remove($postRepository->find($id), true);
        return $this->redirectToRoute('app_poll');
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/poll/new', name: 'app_poll_new')]
    public function new(PollRepository $pollRepository, UserRepository $userRepository, Request $request ,PostRepository $postRepository , PollOptionRepository $pollOptionRepository): Response
    {
        $postForm=$this->createForm(PostFormType::class);
        $pollForm= $this->createForm(PollFormType::class);
        $pollOptionForm=$this->createForm(PollOptionFormType::class);
        $postForm->handleRequest($request);
        $pollForm->handleRequest($request);
        $pollOptionForm->handleRequest($request);
        if ($postForm->isSubmitted() && $postForm->isValid() && $pollForm->isSubmitted() && $pollForm->isValid()) {
            $postData = $postForm->getData();
            $pollData= $pollForm->getData();
            $pollOptionData= $pollOptionForm->getData();
            $user = $userRepository->findOneBy(['id' => $this->getUser()?->getId()]);
            $post = new Post();
            $poll = new Poll();
            $pollOption=new PollOption();
            $options = $_POST['options'];
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
            $postRepository->add($post, true);

            $poll->setEnd($pollData['end']);
            $poll->setPost($post);
            $pollRepository->add($poll, true);
            foreach ($options as $option) {
                $pollOption=new PollOption();
                $pollOption->setValue($option);
                $pollOption->setPoll($poll);
                $pollOptionRepository->add($pollOption);
            }
            $pollOptionRepository->add($pollOption);
            $departments = [];
            foreach ($post->getDepartments() as $department) {
                $departments[] = $department;
            }
            NotificationService::sendNotificationToEagles('New Poll has been published', $post->getName(), $departments, $userRepository);
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }
            return $this->redirectToRoute('app_poll');
        }
        $template = $request->isXmlHttpRequest() ? '_modal.html.twig' : 'polls/index.html.twig';
        return $this->renderForm($template, [
            'postForm' => $postForm->createView(),
            'pollOptionForm' => $pollOptionForm->createView(),
            'pollForm' => $pollForm->createView(),
        ], new Response(null,
            $postForm->isSubmitted() ? 422 : 200
        ));
    }
    #[Route('/poll/add', name: 'app_poll_add')]
    public function add(): Response
    {
        $postForm = $this->createForm(PostFormType::class);
        $pollForm = $this->createForm(PollFormType::class);
        $pollOptionForm = $this->createForm(PollOptionFormType::class);

        return $this->renderForm('polls/new.html.twig', [
            'postForm' => $postForm,
            'pollForm'=>$pollForm,
            'pollOptionForm'=>$pollOptionForm

        ]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/poll/update/submit/{id}', name: 'app_poll_update_submit')]
    public function update(Request $request, PollRepository $pollRepository,Post $post,PostRepository $postRepository , PollOptionRepository $pollOptionRepository, UserRepository $userRepository): Response
    {
        $poll=$pollRepository->findOneBy(['post'=>$post]);
        $option=$pollOptionRepository->findOneBy(['poll'=>$poll]);
        $postForm = $this->createForm(PostFormType::class, $post);
        $pollForm = $this->createForm(PollFormType::class, $poll);
        $pollOptionForm =$this->createForm(PollOptionFormType::class,$option);
        $postForm->handleRequest($request);
        $pollForm->handleRequest($request);
        $pollOptionForm->handleRequest($request);
        if ($postForm->isSubmitted() && $postForm->isValid() && $pollForm->isSubmitted() && $pollForm->isValid()) {
            $postData=$postForm->getData();
            $pollData=$pollForm->getData();
            $options = $_POST['options'];
            $post ->setName($postData->getName());
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
            $poll?->setEnd($pollData->getEnd());
            $pollRepository->add($poll);
            foreach ($poll?->getPollOptions() as $option) {
                $pollOptionRepository->remove($option);
            }
            foreach ($options as $option) {
                $pollOption=new PollOption();
                $pollOption->setValue($option);
                $pollOption->setPoll($poll);
                $pollOptionRepository->add($pollOption);
            }
            $departments = [];
            foreach ($post->getDepartments() as $department) {
                $departments[] = $department;
            }
            NotificationService::sendNotificationToEagles('Poll has been updated', $post->getName(), $departments, $userRepository);
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }
            return $this->redirectToRoute('app_poll');
        }
        $template = $request->isXmlHttpRequest() ? '_modal.edit.html.twig' : 'polls/index.html.twig';
        return $this->renderForm($template,
            [
                'form' => $postForm,
                'extraForm' => $pollForm,
                'secondExtraForm' => null,
                'modalTitle' => 'Edit Poll',
                'routeName' => 'app_poll_update_submit',
                'post' => $post

            ], new Response(null,
                $postForm->isSubmitted() ? 422 : 200
            ));


}
    #[Route('/poll/update/{id}', name: 'app_poll_update')]
    public function edit(Request $request,Post $post, PollRepository $pollRepository, PollOptionRepository $pollOptionRepository): Response
    {
        $poll=$pollRepository->findOneBy(['post'=>$post]);
        $options=$pollOptionRepository->findBy(['poll'=>$poll]);
        $postForm = $this->createForm(PostFormType::class, $post);
        $pollForm= $this->createForm(PollFormType::class, $poll);
        $pollOptionForm =$this->createForm(PollOptionFormType::class , $options);
        $template = $request->query->get('ajax') ? '_modal.edit.html.twig' : 'polls/index.html.twig';
        return $this->renderForm($template,
            [
                'form' => $postForm,
                'extraForm' => $pollForm,
                'secondExtraForm' => null,
                'modalTitle' => 'Edit Poll',
                'routeName' => 'app_poll_update_submit',
                'id' => $post->getId(),
                'post' => $post,
                'options'=>$options,
            ]);
    }
}





