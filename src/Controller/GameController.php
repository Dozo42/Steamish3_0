<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\AccountRepository;
use App\Repository\CommentRepository;
use App\Repository\CountryRepository;
use App\Repository\GameRepository;
use App\Repository\GenreRepository;
use App\Repository\PublisherRepository;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/jeux')]
class GameController extends AbstractController
{
    
    public function __construct(
            private GameRepository $gameRepository,
            private CommentRepository $commentRepository,
            private GenreRepository $genreRepository,
            private CountryRepository $countryRepository,
            private PublisherRepository $publisherRepository,
            private AccountRepository $accountRepository
            ) {
        $this->gameRepository = $gameRepository;
        $this->commentRepository = $commentRepository;
        $this->accountRepository = $accountRepository;
        
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
    public function oneGame($slug = '', Request $request, EntityManagerInterface $em): Response 
    {
        $user= $this->getUser();
        $game= $this->gameRepository->findOneBy(['slug' => $slug]);

        $gameEntity = $this->gameRepository->getGameAllDetails($slug);
        $similarGames = ($this->gameRepository->getSimilarGames($gameEntity));
        $comment = $this->commentRepository->getOneByGameAndAccount( $game, $user );
        $newComment = new Comment();
        $commentAccount = $this->commentRepository->getOneByGameAndAccount($game, $user);
        $form =$this->createForm(CommentType::class, $newComment);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $newComment->setUpVotes(0);
            $newComment->setDownVotes(0);
            $newComment->setCreatedAt(new DateTime());
            $newComment->setAccount($user);
            $newComment->setGame($game);
            $em->persist($form->getData());
            $em->flush();
        }

        return $this->render('game/oneGame.html.twig', [
            'game' => $gameEntity,
            'similar'=> $similarGames,
            'comment'=>$comment,
            'form'=>$form->createView()
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
        $genreEntities = $this->genreRepository->getGenresByAjaxRequest($research);
        $publisherEntities = $this->publisherRepository->getPublishersByAjaxRequest($research);

        return (new JsonResponse())->setData([
            'html' => $this->renderView('common/_search_index.html.twig', [
            'games' => $gameEntities,
            'genres' => $genreEntities,
            'publishers' => $publisherEntities
            ]),
        ]);
    }
}
