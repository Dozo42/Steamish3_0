<?php

namespace App\Controller;

use App\Entity\DirectMessage;
use App\Form\DirectMessagerieType;
use App\Repository\AccountRepository;
use App\Repository\DirectMessageRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessagerieController extends AbstractController
{
    public function __construct(
        private DirectMessageRepository $directMessageRepository,
        private AccountRepository $accountRepository,
        private EntityManagerInterface $em

    )
    {
        
    }

    #[Route('/messagerie', name: 'app_messagerie')]
    public function index(Request $request): Response
    {
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
            'error' => $error
        ]);
    }
}