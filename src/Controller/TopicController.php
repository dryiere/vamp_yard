<?php

namespace App\Controller;

use App\Entity\Topic;
use App\Form\TopicFormType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class TopicController extends AbstractController
{
    #[Route('/', name: 'app_topic')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_topic_list');
//        return $this->render('topic/index.html.twig', [
//            'controller_name' => 'TopicController',
//        ]);
    }

    #[Route('/topic/list', name: 'app_topic_list')]
    public function list(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $query = $entityManager->createQuery('SELECT t FROM App\Entity\Topic t');

        $pagination = $paginator->paginate($query, $request->query->getInt('page', 1), 10);
        return $this->render('topic/list.html.twig', ['pagination' => $pagination]);
    }

    #[Route('/topic/create', name: 'app_topic_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $topic = new Topic();
        if($request->get('id')) {
            $topic = $entityManager
                ->getRepository(Topic::class)
                ->find($request->get('id'));
            if(!$topic instanceof Topic)
                throw new HttpException(404, 'Topic could not be found.');
            if(!in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || $topic->getUserId() !== $this->getUser()->getId())
                throw new HttpException(403, 'You\'re not allowed to edit this topic.');
        }
        $form = $this->createForm(TopicFormType::class, $topic);
        $form->handleRequest($request);
        if(!$topic->getId()) {
            $topic->setUser($this->getUser());
            $topic->setCreatedAt(new \DateTimeImmutable('now'));
        }
        if($form->isSubmitted() && $form->isValid()) {
            $topic->setUpdatedAt(new \DateTimeImmutable('now'));
            $entityManager->persist($topic);
            $entityManager->flush();
            return $this->redirectToRoute('app_topic_view', ['id' => $topic->getId()]);
        }
        return $this->render('topic/create.html.twig', [
            'controller_name' => 'TopicController',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/topic/view', name: 'app_topic_view')]
    public function view(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator)
    {
        $topic = $entityManager->getRepository(Topic::class)->find(['id' => $request->get('id')]);
        if(!$topic instanceof Topic)
            throw new HttpException(404, 'Topic could not be found.');

        $postQuery = $entityManager->createQueryBuilder()
            ->select('p')
            ->from('App\Entity\Post', 'p')
            ->where('p.topic_id=:topic')
            ->setParameter('topic', $topic->getId())
            ->orderBy('p.created_at', 'ASC');

        $postPagination = $paginator->paginate($postQuery, $request->query->getInt('page', 1), 10);

        return $this->render('topic/view.html.twig', [
            'Topic' => $topic,
            'postPagination' => $postPagination,
        ]);
    }
}
