<?php

namespace App\Controller;

use App\Repository\GameRepository;
use App\Repository\PublisherRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/editeur')]
class PublisherController extends AbstractController
{

    public function __construct(PublisherRepository $publisherRepository, GameRepository $gameRepository)
    {
        $this->publisherRepository = $publisherRepository;
        $this->gameRepository = $gameRepository;
    }

    #[Route('/', name: 'app_publisher')]
    public function index(): Response
    {
        return $this->render('publisher/index.html.twig', [
            'publishers' => $this->publisherRepository->getPublishersAll()
        ]);
    }

    #[Route('/{slug}', name: 'app_showPublisher')]
    public function showPublisher($slug = ''): Response
    {
        $publisherEntity = $this->publisherRepository->getPublisherBySlug($slug);
        $game = $this->gameRepository->getCountBoughtGamesByPublisher($publisherEntity);
        $price = 0;

        foreach($game as $jeux ){
          
            $price += $jeux[0]->getPrice()*$jeux[1];
        }
        
        return $this->render('publisher/showPublisher.html.twig', [
            'publisher' => $publisherEntity,
            'price'=> $price
        ]);
    }
}