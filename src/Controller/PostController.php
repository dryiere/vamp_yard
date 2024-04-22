<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Topic;
use App\Form\PostFormType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/post', name: 'app_post')]
    public function index(): Response
    {
        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }
    #[Route('/post/create', name: 'app_post_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $topic = $request->get('topic') ? $entityManager->getRepository(Topic::class)->find($request->get('topic')) : null;
        if(!$topic instanceof Topic)
            throw new HttpException(404, 'Topic could not be found.');

        $post = new Post();
        if($request->get('id')) {
            $post = $entityManager
                ->getRepository(Post::class)
                ->find($request->get('id'));
            if(!$post instanceof Post)
                throw new HttpException(404, 'Post could not be found.');
            if(!in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || $post->getUserId() !== $this->getUser()->getId())
                throw new HttpException(403, 'You\'re not allowed to edit this topic.');
        } else {
            $post->setTopic($topic);
            $post->setUser($this->getUser());
            $post->setCreatedAt(new \DateTimeImmutable('now'));
        }
        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $post->setUpdatedAt(new \DateTimeImmutable('now'));
            $entityManager->persist($post);
            $entityManager->flush();
            return $this->redirectToRoute('app_topic_view', ['id' => $topic->getId()]);
        }
        return $this->render('post/create.html.twig', [
            'Topic' => $topic,
            'Post' => $post,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/post/view', name: 'app_post_view')]
    public function view(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator)
    {
        $post = $entityManager->getRepository(Post::class)->find(['id' => $request->get('id')]);
        if(!$post instanceof Post)
            throw new HttpException(404, 'Post could not be found.');

        $replyQuery = $entityManager->createQueryBuilder()
            ->select('r')
            ->from('App\Entity\Reply', 'r')
            ->where('r.post_id=:post')
            ->andWhere('r.reply_id IS NULL')
            ->setParameter('post', $post->getId())
            ->orderBy('r.created_at', 'ASC');

        $replyPagination = $paginator->paginate($replyQuery, $request->query->getInt('page', 1), 10);

        return $this->render('post/view.html.twig', [
            'Post' => $post,
            'replyPagination' => $replyPagination,
        ]);
    }
}
