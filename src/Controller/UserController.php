<?php

namespace App\Controller;

use App\Repository\AccountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/utilisateur')]
class UserController extends AbstractController
{
    public function __construct(private AccountRepository $accountRepository)
    {
        
    }

    #[Route('/utilisateurs', name: 'app_user_index')]
    public function indexAccount(): Response
    {
        return $this->render('user/index.html.twig', [
            'account'=>$this->accountRepository->findAll()
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
