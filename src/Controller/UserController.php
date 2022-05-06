<?php

namespace App\Controller;

use App\Entity\Account;
use App\Form\AccountType;
use App\Repository\AccountRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

#[Route('/utilisateur')]
class UserController extends AbstractController
{
    public function __construct(private AccountRepository $accountRepository)
    {
        
    }

    #[Route('/', name: 'app_user_index')]
    public function indexAccount(): Response
    {
        return $this->render('user/index.html.twig', [
            'account'=>$this->accountRepository->findBy([], ['createdAt' => 'DESC'])
        ]);
    }
    
    #[Route('/nouveau', name: 'app_new_user')]
    public function createAccount(Request $request, EntityManagerInterface $em): Response
    {
        $form =$this->createForm(AccountType::class, new Account());
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
              /** @var Country $country */
              $account = $form->getData();
              $account->setCreatedAt(new DateTime());
              $account->setWallet(0);
              $em->persist($form->getData());
              $em->flush();
              return $this->redirectToRoute('app_user_index');
        }
        return $this->render('user/new.html.twig',[
            'form'=>$form->createView(),
        ]);

    }

    #[Route('/modifier/{name}', name: 'app_mod_user')]
    public function edit(Account $account, Request $request, EntityManagerInterface $em, string $name): Response
    {
        $form =$this->createForm(AccountType::class, $account);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
              /** @var Country $country */
              $account = $form->getData();
              $em->persist($form->getData());
              $em->flush();
              return $this->redirectToRoute('app_user_index');
        }
        return $this->render('user/modified.html.twig',[
            'account' => $this->accountRepository->getAccountAllDetails($name),
            'form'=>$form->createView(),
        ]);

    }


    #[Route('/{name}', name: 'app_user')]
    public function show(string $name): Response
    {
        return $this->render('user/showAccount.html.twig', [
            'account' => $this->accountRepository->getAccountAllDetails($name)
        ]);
    }


}
