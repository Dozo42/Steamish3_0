<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    public function __construct(private GameRepository $gameRepository, private CommentRepository $commentRepository) {
        $this->gameRepository = $gameRepository;
        $this->commentRepository = $commentRepository;
    }

    #[Route('/jeux', name: 'app_game')]
    public function index(): Response
    {
        $games = $this->gameRepository->findAll();

        return $this->render('game/index.html.twig', [
            'games' => $games,
        ]);
    }

    #[Route('/jeux/{slug}', name: 'app_one_game')]
    public function oneGame($slug = ''): Response 
    {
        $gameEntity = $this->gameRepository->getGameAllDetails($slug);
        // dd($gameEntity);
        return $this->render('game/oneGame.html.twig', [
            'game' => $gameEntity,
        ]);
    }
}
