<?php

namespace App\Controller;

use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    public function __construct(private GameRepository $gameRepository) {
        $this->gameRepository = $gameRepository;
    }

    #[Route('/jeux', name: 'app_game')]
    public function index(): Response
    {
        $games = $this->gameRepository->findAll();

        return $this->render('game/index.html.twig', [
            'games' => $games,
        ]);
    }

    #[Route('/jeux/{{slug}}', name: 'app_one_game')]
    public function oneGame($slug = ''): Response
    {
        

        return $this->render('game/oneGame.html.twig', [
        ]);
    }
}
