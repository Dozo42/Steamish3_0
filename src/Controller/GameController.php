<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\CountryRepository;
use App\Repository\GameRepository;
use App\Repository\GenreRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/jeux')]
class GameController extends AbstractController
{
    
    public function __construct(
            private GameRepository $gameRepository,
            private CommentRepository $commentRepository,
            private GenreRepository $genreRepository,
            private CountryRepository $countryRepository
            ) {
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
    public function showByGenre($slug): Response
    {
        $genreEntity = $this->genreRepository->getGamesByGenre($slug);

        return $this->render('game/gameGenre.html.twig', [
            'genre' => $genreEntity
        ]);
    }

    #[Route('/langue/{slug}', name:'app_game_by_langue')]
    public function showByLanguage($slug): Response
    {
        $languageEntity = $this->countryRepository->getGamesByLanguage($slug);

        return $this->render('game/gameLangue.html.twig', [
            'language' => $languageEntity
        ]);
    }

    #[Route('/ajax/search_engine/{research}', name:'app_research')]
    public function ajaxResearch($research): JsonResponse {

        $gameEntities = $this->gameRepository->getGamesByAjaxRequest($research);
        return (new JsonResponse())->setData([
            'html' => $this->renderView('common/_search_index.html.twig', [
                'games' => $gameEntities,
            ]),
        ]);
    }
}
