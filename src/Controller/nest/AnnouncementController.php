<?php

namespace App\Controller\nest;

use App\Entity\Announcement;
use App\Entity\Image;
use App\Entity\Post;
use App\form\AnnouncementFormType;
use App\form\PostFormType;
use App\Repository\AnnouncementRepository;
use App\Repository\DepartmentRepository;
use App\Repository\ImageRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Service\FileUploaderService;
use App\Service\NotificationService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AnnouncementController extends AbstractController
{
    #[Route('/announcement', name: 'app_announcement')]
    public function index(Request $request, DepartmentRepository $departmentRepository,UserRepository $userRepository,AnnouncementRepository $announcementRepository, PostRepository $postRepository   ): Response
    {
        $template = $request->query->get('ajax') ? 'announcements/table.html.twig' : 'announcements/index.html.twig';
        $announcementForm = $this->createForm(AnnouncementFormType::class);
        $postForm = $this->createForm(PostFormType::class);
        $user = $userRepository->find($this->getUser()?->getId());
        $roles  = ['ROLE_IT','ROLE_BUSINESS','ROLE_DEVCO','ROLE_MARKETING'];
        if (!in_array($user?->getRoles()[0], $roles, true)) {
            $announcements = $postRepository->findAnnouncements();
        }else{
            $announcements = $postRepository->findAnnouncementsByDepartments([$user?->getDepartment(),$departmentRepository->findOneBy(['name' => 'All'])]);
        }

        return $this->render($template, [
            'announcements' => $announcements,
            //'form' => $form,
            'postForm' => $postForm->createView(),
            'announcementForm' => $announcementForm->createView(),
        ]);
    }

    #[Route('/announcement/add', name: 'app_announcement_add')]
    public function add(): Response
    {
        $postForm = $this->createForm(PostFormType::class);
        $announcementForm = $this->createForm(AnnouncementFormType::class);
        return $this->renderForm('announcements/new.html.twig', [
            'postForm' => $postForm->createView(),
            'announcementForm'=>$announcementForm->createView(),

        ]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/announcement/new', name: 'app_announcement_new')]
    public function new(AnnouncementRepository $announcementRepository, Request $request ,PostRepository $postRepository , ImageRepository $imageRepository, UserRepository $userRepository): Response
    {
        $postForm=$this->createForm(PostFormType::class);
        $announcementForm = $this->createForm(AnnouncementFormType::class);
        $postForm->handleRequest($request);
        $announcementForm->handleRequest($request);
        if ($postForm->isSubmitted() && $postForm->isValid() && $announcementForm->isSubmitted() && $announcementForm->isValid()) {
            $targetDirectory='uploads/announcement/';
            $postData = $postForm->getData();
            $announcementData= $announcementForm->getData();
            $post = new Post();
            $announcement = new Announcement();
            $post->setName($postData['name']);
            $post->setPublishDate(new \DateTimeImmutable());
            $post->setAuthor($this->getUser());
            foreach ($postData['departments'] as $department) {
                $post->addDepartment($department);
            }
            $postRepository->add($post, true);
            $announcement->setContent($announcementData['content']);
            $announcement->setPost($post);
            $announcementRepository->add($announcement, true);
            //FileUploaderService::uploadAnnouncementImages($announcementImages , $targetDirectory ,$announcement, $announcementRepository,$imageRepository);
            $departments = [];
            foreach ($post->getDepartments() as $department) {
                $departments[] = $department;
            }
            $this->uploadAnnouncement($announcement->getId(), $announcementRepository, $imageRepository);
            NotificationService::sendNotificationToEagles('New Announcement has been published', $announcement->getContent(), $departments, $userRepository);
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }
            return $this->redirectToRoute('app_announcement');
        }
        $template = $request->isXmlHttpRequest() ? '_modal.html.twig' : 'announcements/index.html.twig';
        return $this->renderForm($template, [
            'forms' => [$postForm->createView(), $announcementForm->createView()],
        ],new Response(null,
            $postForm->isSubmitted() ? 422 : 200
        ));
    }



    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */

    #[Route('/announcement/update/{id}', name: 'app_announcement_update')]
    public function edit(Request $request ,Post $post , AnnouncementRepository $announcementRepository): Response
    {
        $announcement=$announcementRepository->find($request->query->get('announcementId'));
        $postForm = $this->createForm(PostFormType::class, $post);
        $announcementForm = $this->createForm(AnnouncementFormType::class, $announcement);
        $template = $request->query->get('ajax') ? '_modal.edit.html.twig' : 'announcements/index.html.twig';
        return $this->renderForm($template,
            [
                'form'=> $postForm ,
                'extraForm'=> $announcementForm,
                'secondExtraForm' => null,
                'announcement'=>$announcement,
                'modalTitle' => 'Edit Announcement',
                'routeName' => 'app_announcement_update_submit',
                'id' => $post->getId(),

            ]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/announcement/update/submit/{id}', name: 'app_announcement_update_submit')]
    public function update(Request $request, AnnouncementRepository $announcementRepository,Post $post,PostRepository $postRepository , ImageRepository $imageRepository , UserRepository $userRepository): Response
    {
        $announcement=$announcementRepository->findOneBy(['post'=>$post]);
        $postForm = $this->createForm(PostFormType::class, $post);
        $announcementForm = $this->createForm(AnnouncementFormType::class, $announcement);
        $postForm->handleRequest($request);
        $announcementForm->handleRequest($request);
        if ($postForm->isSubmitted() && $postForm->isValid() && $announcementForm->isSubmitted() && $announcementForm->isValid()) {
            //$announcementImages = $_FILES['announcementImages'];
            $targetDirectory='uploads/announcement/';
            $postData=$postForm->getData();
            $announcementData=$announcementForm->getData();
            $post ->setName($postData->getName());
            $postRepository->add($post);
            $announcement?->setContent($announcementData->getContent());
            $announcementRepository->add($announcement);
            $this->uploadAnnouncement($announcement?->getId(), $announcementRepository, $imageRepository);
            //FileUploaderService::uploadAnnouncementImages($announcementImages , $targetDirectory ,$announcement, $announcementRepository,$imageRepository);
            $departments = [];
            foreach ($post->getDepartments() as $department) {
                $departments[] = $department;
            }
            NotificationService::sendNotificationToEagles('New Announcement has been updated', $announcement->getContent(), $departments, $userRepository);
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }

        }
        $template = $request->isXmlHttpRequest() ? '_modal.edit.html.twig' : 'announcements/index.html.twig';
        return $this->renderForm($template, [
            'form' => $postForm,
            'extraForm' => $announcementForm,
            'modalTitle' => 'Edit Announcement',
            'routeName' => 'app_announcement',
        ],new Response(null,
            $postForm->isSubmitted() ? 422 : 200
        ));


}
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/announcement/delete/{id}', name: 'app_announcement_delete')]
    public function delete( PostRepository $postRepository , $id ): Response
    {
        $post = $postRepository->find($id);
        $postRepository->remove($post, true);
        return $this->redirectToRoute('app_announcement');

    }
    #[Route('/announcement/deleteImage/{id}', name: 'app_announcement_delete_image')]
    public function deleteImage(Image $image, ImageRepository $imageRepository ): Response
    {

        unlink('uploads/announcement/'.$image->getImageName());
        $imageRepository->remove($image , true);

        return $this->redirectToRoute('app_announcement');



    }

    #[Route('/test', name: 'app_test')]
    public function test(Request $request): Response
    {
        dd($request->files->get('file'));
    }

    #[Route('/api/test/{dep}', name: 'test_post', methods: ['POST', 'GET'])]
    public function testPost($dep, DepartmentRepository $departmentRepository,Request $request, SerializerInterface $serializer, PostRepository $postRepository): JsonResponse
    {
        $departments = $departmentRepository->findBy(['name' => [$dep, 'ALL']]);
        $posts = $postRepository->findByDepartments($departments);
        $json = $serializer->serialize($posts, 'json', [
            'groups' => 'post:read',
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
        ]);
        return new JsonResponse($json, 200, ['Content-Type' => 'application/json'], true);
    }

    #[Route('/upload/announcement', name: 'app_announcement_upload', methods: ['POST'])]
    public function uploadAnnouncement($id,AnnouncementRepository $announcementRepository, ImageRepository $imageRepository): JsonResponse
    {
        $announcement = $announcementRepository->find($id);
        $images = $_FILES;
        $targetDirectory = 'uploads/announcement/';
        $images = FileUploaderService::uploadAnnouncementImages($images, $targetDirectory, $announcement, $announcementRepository, $imageRepository);
        $json = json_encode($images);
        return new JsonResponse($json, 200, ['Content-Type' => 'application/json'], true);
    }

}





