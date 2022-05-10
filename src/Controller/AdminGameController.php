<?php

namespace App\Controller;

use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminGameController extends AbstractController
{
    public function __construct(
        private GameRepository $gameRepository
    )
    {
        
    }

    #[Route('/admin/game', name: 'app_admin_game')]
    public function index(): Response
    {
        return $this->render('admin_game/index.html.twig', [
            'games' => $this->gameRepository->findAll()
        ]);
    }
    
    #[Route('/admin/game/detail/{slug}', name: 'app_admin_game_detail')]
    public function show(string $slug): Response
    {
        return $this->render('admin_game/show.html.twig', [
            'game' => $this->gameRepository->getGameAllDetails($slug)
        ]);
    }

    #[Route('/admin/game/ajouter', name: 'app_admin_game_add')]
    public function add(string $slug): Response
    {
        return $this->render('admin_game/show.html.twig', [
            'game' => $this->gameRepository->getGameAllDetails($slug)
        ]);
    }
}
