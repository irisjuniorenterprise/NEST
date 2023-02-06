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
use App\Repository\PollOptionRepository;
use App\Repository\PollRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PollController extends AbstractController
{
    #[Route('/poll', name: 'app_poll')]
    public function index( PollRepository $pollRepository , PostRepository $postRepository): Response
    {
        $post=$postRepository->find('1');

        $form = $this->createForm(PollFormType::class);
        return $this->renderForm('polls/index.html.twig', [
            'polls' => $pollRepository->findAll(),
            'form' => $form,
        ]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/poll/delete/{id}', name: 'app_poll_delete')]
    public function delete( PostRepository $postRepository , $id ): Response
    {


        $postRepository->remove($postRepository->find($id), true);
        return $this->redirectToRoute('app_poll');

    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/poll/new', name: 'app_poll_new')]
    public function new(PollRepository $pollRepository, Request $request ,PostRepository $postRepository , PollOptionRepository $pollOptionRepository): Response
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
            $post = new Post();
            $poll = new Poll();
            $pollOption=new PollOption();
            $post->setName($postData['name']);
            $post->setPublishDate(new \DateTimeImmutable());
            $post->setAuthor($this->getUser());
            $postRepository->add($post, true);

            $poll->setEnd($pollData['end']);
            $poll->setPost($post);
            $pollRepository->add($poll, true);

            $pollOption->setValue($pollOptionData['value']);
            $pollOption->setPoll($poll);
            $pollOptionRepository->add($pollOption);
            return $this->redirectToRoute('app_poll');
        }
        return $this->redirectToRoute('app_poll');
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
    public function update(Request $request, PollRepository $pollRepository,PostFormType $postFormType,Post $post,PostRepository $postRepository , PollOptionRepository $pollOptionRepository): Response
    {
        $poll=$pollRepository->find($request->query->get('pollId'));
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
            $pollOptionData=$pollOptionForm->getData();
            $post ->setName($postData->getName());
            $postRepository->add($post);
            $poll->setEnd($pollData->getEnd());
            $option ->setValue($pollOptionData->getValue());
            $pollRepository->add($poll);

        }
        return $this->redirectToRoute('app_poll');


}
    #[Route('/poll/update/{id}', name: 'app_poll_update')]
    public function edit(Request $request,PostFormType $postFormType,Post $post ,$id , PollRepository $pollRepository , PollOptionRepository $pollOptionRepository): Response
    {

        $poll=$pollRepository->find($request->query->get('pollId'));
        $option=$pollOptionRepository->findOneBy(['poll'=>$poll]);
        $postForm = $this->createForm(PostFormType::class, $post);
        $pollForm= $this->createForm(PollFormType::class, $poll);
        $pollOptionForm =$this->createForm(PollOptionFormType::class , $option);
        return $this->renderForm('polls/edit.html.twig',
        [
            'postForm'=> $postForm ,
            'pollForm'=> $pollForm,
            'poll' => $poll,
            'pollOptionForm'=>$pollOptionForm


        ]);
    }
}





