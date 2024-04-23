<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Reply;
use App\Form\ReplyFormType;
use App\Form\TopicFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class ReplyController extends AbstractController
{
    #[Route('/reply', name: 'app_reply')]
    public function index(): Response
    {
        return $this->render('reply/index.html.twig', [
            'controller_name' => 'ReplyController',
        ]);
    }
    #[Route('/reply/create', name: 'app_reply_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reply = new Reply();
        if($request->get('id')) {
            $reply = $entityManager->getRepository(Reply::class)->find($request->get('id'));
            if(!$reply instanceof Reply)
                throw new HttpException(404, 'Reply could not be found.');
            $post = $reply->getPost();
        } else {
            $replyParent = null;
            if($request->get('parent')) {
                $replyParent = $entityManager->getRepository(Reply::class)->find($request->get('parent'));
                if(!$replyParent instanceof Reply)
                    throw new HttpException(404, 'Parent reply could not be found.');
                $post = $replyParent->getPost();
            } else {
                $post = $entityManager->getRepository(Post::class)->find($request->get('post'));
            }
            $reply->setUser($this->getUser());
            $reply->setPost($post);
            if($replyParent instanceof Reply)
                $reply->setReplyId($replyParent);
            $reply->setCreatedAt(new \DateTimeImmutable('now'));
        }
        $form = $this->createForm(ReplyFormType::class, $reply);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $reply->setUpdatedAt(new \DateTimeImmutable('now'));
            $entityManager->persist($reply);
            $entityManager->flush();
            return $this->redirectToRoute('app_post_view', ['id' => $post->getId()]);
        }
        return $this->render('reply/create.html.twig', [
            'Post' => $post,
            'Reply' => $reply,
            'ReplyParent' => $reply->getReplyId(),
            'form' => $form->createView(),
        ]);
    }
    #[Route('/reply/view', name: 'app_reply_view')]
    public function view(Request $request, EntityManagerInterface $entityManager): Response
    {

    }
}
