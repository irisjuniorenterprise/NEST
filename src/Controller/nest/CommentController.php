<?php

namespace App\Controller\nest;

use App\Entity\Post;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    // get all comments with post id
    #[Route('/post/comment/{id}', name: 'app_post_comment')]
    public function index(Post $post, CommentRepository $commentRepository, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $comments = $commentRepository->findBy(['post' => $post]);
        $template = $request->query->get('ajax') ? 'comment/_modal.comment.html.twig' : 'comment/index.html.twig';
        return $this->render($template, [
            'comments' => $comments,
            'modalTitle' => 'Comments for : ' . $post->getName(),
        ]);
    }
}
