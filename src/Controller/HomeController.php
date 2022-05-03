<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(GameRepository $gameRepository, CommentRepository $commentRepository): Response
    {
        $lastPublishedGames = $gameRepository->getGamesByDate();
        $lastPostedComments = $commentRepository->getLastPostedComments();

        return $this->render('home/index.html.twig', [
            'lastPublishedGames' => $lastPublishedGames,
            'lastPostedComments' => $lastPostedComments
        ]);
    }

}
