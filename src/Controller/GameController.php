<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/jeux')]
class GameController extends AbstractController
{
    
    public function __construct(private GameRepository $gameRepository, private CommentRepository $commentRepository) {
        $this->gameRepository = $gameRepository;
        $this->commentRepository = $commentRepository;
    }

    #[Route('/', name: 'app_game')]
    public function index(): Response
    {
        $games = $this->gameRepository->findAll();

        return $this->render('game/index.html.twig', [
            'games' => $games,
        ]);
    }

    #[Route('/{slug}', name: 'app_one_game')]
    public function oneGame($slug = ''): Response 
    {
        $gameEntity = $this->gameRepository->getGameAllDetails($slug);
        $similarGames = ($this->gameRepository->getSimilarGames($gameEntity));
        // dd($gameEntity);
        return $this->render('game/oneGame.html.twig', [
            'game' => $gameEntity,
            'similar'=> $similarGames
        ]);
    }

    
    #[Route('/{slug}/avis', name: 'app_avis')]
    public function comments($slug = ''): Response
    {
        $gameEntity = $this->gameRepository->getGameAllDetails($slug);

        return $this->render('game/comments.html.twig', [
            'game' => $gameEntity,
        ]);
    }

    #[Route('/genre/{slug}', name:'app_game_by_genre')]
    public function gameByGenre($slug): Response
    {
        return $this->render('game/gameGenre.html.twig', [
        ]);
    }
}
