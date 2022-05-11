<?php

namespace App\Controller;

use App\Entity\Topic;
use App\Form\TopicType;
use App\Repository\ForumRepository;
use App\Repository\TopicRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TopicController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ForumRepository $forumRepository,
        private TopicRepository $topicRepository
    )
    {
        
    }

    #[Route('forum/topic/{forumId}/ajouter', name: 'app_topic_add')]
    public function add(Request $request, int $forumId): Response
    {
        $forumEntity = $this->forumRepository->find($forumId);
        $topicEntity = new Topic();

        $form = $this->createForm(TopicType::class, $topicEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $topicEntity->setCreatedAt(new DateTime());
            $topicEntity->setCreatedBy($this->getUser());
            $topicEntity->setForum($forumEntity);
            $this->em->persist($form->getData());
            $this->em->flush();
            return $this->redirectToRoute('app_forum_topic', ['id' => $forumId]);

        }

        return $this->render('topic/add.html.twig', [
            'form' => $form->createView(),
            'forum' => $forumEntity
        ]);
    }

    #[Route('/forum/{forumId}/topic/{id}/supprimer', name: 'app_topic_delete')]
    public function delete(int $id, int $forumId): Response
    {

        $topicEntity = $this->topicRepository->find($id);
        $this->em->remove($topicEntity);
        $this->em->flush();

        return $this->redirectToRoute('app_forum_topic', ['id' => $forumId]);
    }
}
