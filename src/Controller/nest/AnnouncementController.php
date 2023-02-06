<?php

namespace App\Controller\nest;

use App\Entity\Announcement;
use App\Entity\Image;
use App\Entity\Post;
use App\form\AnnouncementFormType;
use App\form\PostFormType;
use App\Repository\AnnouncementRepository;
use App\Repository\ImageRepository;
use App\Repository\PostRepository;
use App\Service\FileUploaderService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnnouncementController extends AbstractController
{
    #[Route('/announcement', name: 'app_announcement')]
    public function index(AnnouncementRepository $announcementRepository , PostRepository $postRepository): Response
    {
        $post=$postRepository->find('1');

        $form = $this->createForm(AnnouncementFormType::class);
        return $this->renderForm('announcements/index.html.twig', [
            'announcements' => $announcementRepository->findAll(),
            'form' => $form,
        ]);
    }

    #[Route('/announcement/add', name: 'app_announcement_add')]
    public function add(): Response
    {
        $postForm = $this->createForm(PostFormType::class);
        $announcementForm = $this->createForm(AnnouncementFormType::class);

        return $this->renderForm('announcements/new.html.twig', [
            'postForm' => $postForm,
            'announcementForm'=>$announcementForm

        ]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/announcement/new', name: 'app_announcement_new')]
    public function new(AnnouncementRepository $announcementRepository, Request $request ,PostRepository $postRepository , ImageRepository $imageRepository): Response
    {
        $postForm=$this->createForm(PostFormType::class);
        $announcementForm = $this->createForm(AnnouncementFormType::class);
        $postForm->handleRequest($request);
        $announcementForm->handleRequest($request);
        if ($postForm->isSubmitted() && $postForm->isValid() && $announcementForm->isSubmitted() && $announcementForm->isValid()) {
            $announcementImages = $_FILES['announcementImages'];
            $targetDirectory='uploads/announcement/';
            $postData = $postForm->getData();
            $announcementData= $announcementForm->getData();
            $post = new Post();
            $announcement = new Announcement();
            $post->setName($postData['name']);
            $post->setPublishDate(new \DateTimeImmutable());
            $post->setAuthor($this->getUser());
            $postRepository->add($post, true);
            $announcement->setContent($announcementData['content']);
            $announcement->setPost($post);
            $announcementRepository->add($announcement, true);
            FileUploaderService::uploadAnnouncementImages($announcementImages , $targetDirectory ,$announcement, $announcementRepository,$imageRepository);
            return $this->redirectToRoute('app_announcement');
        }
        return $this->redirectToRoute('app_announcement');
    }



    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */

    #[Route('/announcement/update/{id}', name: 'app_announcement_update')]
    public function edit(Request $request,PostFormType $postFormType,Post $post ,$id , AnnouncementRepository $announcementRepository): Response
    {
        $announcement=$announcementRepository->find($request->query->get('announcementId'));
        $postForm = $this->createForm(PostFormType::class, $post);
        $announcementForm = $this->createForm(AnnouncementFormType::class, $announcement);
        return $this->renderForm('announcements/edit.html.twig',
            [
                'postForm'=> $postForm ,
                'announcementForm'=> $announcementForm,
                'announcement'=>$announcement

            ]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/announcement/update/submit/{id}', name: 'app_announcement_update_submit')]
    public function update(Request $request, AnnouncementRepository $announcementRepository,Post $post,PostRepository $postRepository , ImageRepository $imageRepository ): Response
    {
        $announcement=$announcementRepository->find(($request->query->get('announcementId')));
        $postForm = $this->createForm(PostFormType::class, $post);
        $announcementForm = $this->createForm(AnnouncementFormType::class, $announcement);
        $postForm->handleRequest($request);
        $announcementForm->handleRequest($request);
        if ($postForm->isSubmitted() && $postForm->isValid() && $announcementForm->isSubmitted() && $announcementForm->isValid()) {
            $announcementImages = $_FILES['announcementImages'];
            $targetDirectory='uploads/announcement/';
            $postData=$postForm->getData();
            $announcementData=$announcementForm->getData();
            $post ->setName($postData->getName());
            $postRepository->add($post);
            $announcement->setContent($announcementData->getContent());
            $announcementRepository->add($announcement);
            FileUploaderService::uploadAnnouncementImages($announcementImages , $targetDirectory ,$announcement, $announcementRepository,$imageRepository);

        }
        return $this->redirectToRoute('app_announcement');


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


}





