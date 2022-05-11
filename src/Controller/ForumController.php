<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\ForumRepository;
use App\Repository\MessageRepository;
use App\Repository\TopicRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForumController extends AbstractController
{
    public function __construct(
        private ForumRepository $forumRepository,
        private PaginatorInterface $paginator,
        private TopicRepository $topicRepository,
        private MessageRepository $messageRepository,
        private EntityManagerInterface $em
    )
    {
        
    }

    #[Route('/forum', name: 'app_forum')]
    public function index(Request $request): Response
    {
        $qb = $this->forumRepository->getQbAll();
        $pagination = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('forum/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    #[Route('/forum/{id}', name: 'app_forum_topic')]
    public function show(Request $request, int $id): Response
    {
        $forumEntity = $this->forumRepository->find($id);
        $qb = $this->topicRepository->getQbAll($id);
        $pagination = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('forum/show.html.twig', [
            'pagination' => $pagination,
            'forum' => $forumEntity,
            'user' => $this->getUser()
        ]);
    }

    #[Route('/forum/{forumId}/topic/{topicId}', name: 'app_forum_topic_detail')]
    public function showTopic(Request $request, int $forumId, int $topicId): Response
    {
        $userEntity = $this->getUser();
        $topicEntity = $this->topicRepository->find($topicId);
        $qb = $this->messageRepository->getQbAll($topicId);
        $pagination = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );

        $messageEntity = new Message();
        $form = $this->createForm(MessageType::class, $messageEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $messageEntity->setCreatedAt(new DateTime());
            $messageEntity->setCreatedBy($userEntity);
            $messageEntity->setTopic($topicEntity);

            $this->em->persist($form->getData());
            $this->em->flush();
            return $this->redirectToRoute('app_forum_topic_detail', ['forumId' => $forumId, 'topicId' => $topicId]);
        }

        return $this->render('forum/showTopic.html.twig', [
            'pagination' => $pagination,
            'topic' => $topicEntity,
            'user' => $userEntity,
            'form' => $form->createView()
        ]);
    }
}
