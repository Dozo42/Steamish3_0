<?php

namespace App\Controller;

use App\Form\MessageType;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    public function __construct(
        private MessageRepository $messageRepository,
        private EntityManagerInterface $em,

    )
    {
        
    }

    #[Route('/forum/message/{forumId}/{id}/modifier', name: 'app_message_modify')]
    public function modify(int $id, int $forumId, Request $request): Response
    {
        $messageEntity = $this->messageRepository->find($id);
        $form = $this->createForm(MessageType::class, $messageEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($form->getData());
            $this->em->flush();
            return $this->redirectToRoute('app_forum_topic_detail', ['forumId' => $forumId, 'topicId' => $messageEntity->getTopic()->getId()]);
        }

        return $this->render('message/modify.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/forum/message/{forumId}/{id}supprimer', name: 'app_message_delete')]
    public function delete(int $id, int $forumId): Response
    {
        $messageEntity = $this->messageRepository->find($id);
        $this->em->remove($messageEntity);
        $this->em->flush();

        return $this->redirectToRoute('app_forum_topic_detail', ['forumId' => $forumId, 'topicId' => $messageEntity->getTopic()->getId()]);    }
}
