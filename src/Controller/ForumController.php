<?php

namespace App\Controller;

use App\Repository\ForumRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForumController extends AbstractController
{
    public function __construct(
        private ForumRepository $forumRepository,
        private PaginatorInterface $paginator
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
}
