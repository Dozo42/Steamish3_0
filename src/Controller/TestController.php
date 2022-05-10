<?php

namespace App\Controller;

use App\Entity\Publisher;
use App\Form\PublisherType;
use App\Repository\AccountRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class TestController extends AbstractController
{
    private AccountRepository $accountRepository;
    private PaginatorInterface $paginator;

    public function __construct(AccountRepository $accountRepository, PaginatorInterface $paginator)
    {
        $this->accountRepository = $accountRepository;
        $this->paginator = $paginator;
    }

    #[Route('/test', name: 'app_test')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
       
        // $qb = $this->accountRepository->getQbAll();
        // // pour la pagination
        // $pagination = $this->paginator->paginate(
        //     $qb,
        //     $request->query->getInt('page', 1),
        //     50
        // );

        $publisherEntity = new Publisher();
        $form =$this->createForm(PublisherType::class, $publisherEntity);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $publisherEntity->setCreatedAt(new DateTime());
            $em->persist($form->getData());
            $em->flush();
        }

        // recupere le dump du User connecté
        // $user = $this->getUser();
        // dd($user);

        return $this->render('test/index.html.twig', [
            // 'pagination'=> $pagination,
            'controller_name' => 'TestController',
            // 'form'=>$form->createView()
        ]);
    }
}