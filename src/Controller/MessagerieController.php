<?php

namespace App\Controller;

use App\Entity\DirectMessage;
use App\Form\DirectMessagerieType;
use App\Repository\AccountRepository;
use App\Repository\DirectMessageRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessagerieController extends AbstractController
{
    public function __construct(
        private DirectMessageRepository $directMessageRepository,
        private AccountRepository $accountRepository,
        private EntityManagerInterface $em,
        private PaginatorInterface $paginator

    )
    {
        
    }

    #[Route('/messagerie', name: 'app_messagerie')]
    public function index(Request $request): Response
    {
        /**@var Account $account */
        $account = $this->getUser();

        $newMessageEntities = $this->directMessageRepository->findBy(['hasBeenSeen' => false, 'receiver' => $account ]);
        
        $messageReceivedEntities = $this->directMessageRepository->findBy(['hasBeenSeen' => true, 'receiver' => $account ], [], 10);
        $messageSentEntities = $this->directMessageRepository->findBy(['createBy' => $account ], [], 10);

        foreach ($newMessageEntities as $newMessage) {
            $newMessage->setHasBeenSeen(true);
        }
        $this->em->flush();

        $error = false;
        $directMessageEntity = new DirectMessage();
        $form = $this->createForm(DirectMessagerieType::class);
        $form->handleRequest($request);
   
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $receiverEntity = $this->accountRepository->findOneBy(['email' => $data['email']]);
            if ($receiverEntity != null) {

                $directMessageEntity->setReceiver($receiverEntity);
                $directMessageEntity->setCreateBy($this->getUser());
                $directMessageEntity->setContent($data['content']);
                $directMessageEntity->setCreatedAt(new DateTime());
                $directMessageEntity->setHasBeenSeen(false);

                $this->em->persist($directMessageEntity);
                $this->em->flush();

            } else {
                $error = true;
            }
        }

        return $this->render('messagerie/index.html.twig', [
            'form' => $form->createView(),
            'messageReceived' => $messageReceivedEntities,
            'messageSent' => $messageSentEntities,
            'newMessages' => $newMessageEntities,
            'error' => $error
        ]);
    }

    #[Route('/messagerie/message/envoyer', name: 'app_messagerie_sent_all')]
    public function showSent(Request $request): Response
    {
        /**@var Account $account */
        $account = $this->getUser();

        $qb = $this->directMessageRepository->getQbSent($account);
        $pagination = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('messagerie/showSent.html.twig', [
            'pagination' => $pagination
        ]);
    }


    #[Route('/messagerie/message/recu', name: 'app_messagerie_received_all')]
    public function showReceivd(Request $request): Response
    {
        /**@var Account $account */
        $account = $this->getUser();

        $qb = $this->directMessageRepository->getQbReceived($account, true);
        $pagination = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('messagerie/showReceived.html.twig', [
            'pagination' => $pagination
        ]);
    }
}
