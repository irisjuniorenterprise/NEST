<?php

namespace App\Controller\nest;

use App\Entity\Meeting;
use App\form\MeetingFormType;
use App\Repository\MeetingRepository;
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
    public function index(MeetingRepository $meetingRepository): Response
    {
        $form=$this-> createForm(MeetingFormType::class);
        return $this->renderForm('meetings/index.html.twig', [
            'meetings' => $meetingRepository->findAll(),
            'form' => $form
        ]);
    }

    #[Route('/meeting/add', name: 'app_meeting_add')]
    public function add():Response
    {
        $postForm = $this->createForm(PostFormType::class);
        $engagementPostForm = $this->createForm(EngagementPostFormType::class);
        $meetingForm =$this->createForm(MeetingFormType::class);
        return $this->renderForm('meetings/add.html.twig',
            [
                'postForm'=>$postForm,
                'engagementPostForm'=>$engagementPostForm,
                'meetingForm'=>$meetingForm
            ]);
    }

    #[Route('/meeting/new', name: 'app_meeting_new')]
    public function new(Request $request, PostRepository $postRepository ,EngagementPostRepository $engagementPostRepository, MeetingRepository $meetingRepository , WorkPostRepository $workPostRepository ,  ): Response
    {
        $postForm = $this->createForm(PostFormType::class);
        $engagementPostForm = $this->createForm(EngagementPostFormType::class);
        $meetingForm=$this->createForm(MeetingFormType::class);
        $postForm->handleRequest($request);
        $engagementPostForm->handleRequest($request);
        $meetingForm-> handleRequest($request);

        if ($postForm->isSubmitted() && $postForm->isValid() && $engagementPostForm->isSubmitted() && $engagementPostForm->isValid()) {
            $postData = $postForm->getData();
            $engagementPostData = $engagementPostForm->getData();
            $meetingData = $meetingForm->getData();
            $post = new Post();
            $engagementPost= new EngagementPost();
            $workPost= new WorkPost();
            $meeting = new Meeting();
            $post->setName($postData['name']);
            $post->setPublishDate(new \DateTimeImmutable());
            $post->setAuthor($this->getUser());
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
            return $this->redirectToRoute('app_meeting');
        }
        return $this->redirectToRoute('app_meeting');
    }
#[Route('/meeting/delete/{id}', name: 'app_meeting_delete')]
public function delete( Post $post , PostRepository $postRepository ):Response
{
    $postRepository->remove($post, true);
    return $this->redirectToRoute('app_meeting');

}
    #[Route('/meeting/update/{id}', name: 'app_meeting_update')]
    public function edit(Request $request , Post $post , EngagementPostRepository $engagementPostRepository , MeetingRepository $meetingRepository , WorkPostRepository $workPostRepository ):Response
    {
        $postForm = $this->createForm(PostFormType::class , $post );
        $engagementPost = $engagementPostRepository->findOneBy(['post'=>$post]);
        $engagementPostForm = $this->createForm(EngagementPostFormType::class , $engagementPost);
        $workPost= $workPostRepository->findOneBy(['engagementPost'=>$engagementPost]);
        $meeting= $meetingRepository->findOneBy(['workPost'=>$workPost]);
        $meetingForm =$this->createForm(MeetingFormType::class , $meeting);
        return $this->renderForm('meetings/edit.html.twig',
            [
                'post'=>$post,
                'postForm'=>$postForm,
                'engagementPostForm'=>$engagementPostForm,
                'meetingForm'=>$meetingForm
            ]);
    }
    #[Route('/meeting/update/submit/{id}', name: 'app_meeting_update_submit')]
    public function update(Request $request, PostRepository $postRepository ,EngagementPostRepository $engagementPostRepository, MeetingRepository $meetingRepository , WorkPostRepository $workPostRepository ,Post $post   ): Response
    {
        $postForm = $this->createForm(PostFormType::class , $post);
        $engagementPost = $engagementPostRepository->findOneBy(['post'=>$post]);
        $engagementPostForm = $this->createForm(EngagementPostFormType::class , $engagementPost);
        $workPost= $workPostRepository->findOneBy(['engagementPost'=>$engagementPost]);
        $meeting= $meetingRepository->findOneBy(['workPost'=>$workPost]);
        $meetingForm=$this->createForm(MeetingFormType::class , $meeting);
        $postForm->handleRequest($request);
        $engagementPostForm->handleRequest($request);
        $meetingForm-> handleRequest($request);

        if ($postForm->isSubmitted() && $postForm->isValid() && $engagementPostForm->isSubmitted() && $engagementPostForm->isValid()) {
            $postData = $postForm->getData();
            $engagementPostData = $engagementPostForm->getData();
            $meetingData = $meetingForm->getData();

            $post->setName($postData->getName());

            $postRepository->add($post);
            $engagementPost->setPlace($engagementPostData->getPlace());
            $engagementPost->setLink($engagementPostData->getLink());
            $engagementPost->setStart($engagementPostData->getStart());
            $engagementPost->setEnd($engagementPostData->getEnd());
            $engagementPost->setPost($post);
            $engagementPostRepository->add($engagementPost);
            $workPost->setEngagementPost($engagementPost);
            $workPostRepository->add($workPost);
            $meeting->setType($meetingData->getType());
            $meetingRepository->add($meeting);
            return $this->redirectToRoute('app_meeting');
        }
        return $this->redirectToRoute('app_meeting');
    }
}
