<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\GameSearchType;
use App\Form\GameType;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminGameController extends AbstractController
{
    public function __construct(
        private GameRepository $gameRepository,
        private PaginatorInterface $paginator,
        private EntityManagerInterface $em
    )
    {
        
    }

    #[Route('/admin/game', name: 'app_admin_game')]
    public function index(Request $request): Response
    {
        $qb = $this->gameRepository->getQbAll();

        $form = $this->createForm(GameSearchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
// dump($data);
            if($data['name']){
                $qb->andWhere('g.name = :mavar')
                ->setParameter('mavar', $data['name']);
            }
        }

        $pagination = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('admin_game/index.html.twig', [
            'pagination' => $pagination,
            'form'=> $form->createView() 
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
    public function add(Request $request): Response
    {
        $gameEntity = new Game();
        $form = $this->createForm(GameType::class, $gameEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isvalid()) {
            $this->em->persist($form->getData());
            $this->em->flush();
            return $this->redirectToRoute('app_admin_game');
        }

        return $this->render('admin_game/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/game/modifier/{slug}', name: 'app_admin_game_modify')]
    public function modify(string $slug, Request $request): Response
    {
        $gameEntity = $this->gameRepository->findOneBy(['slug' => $slug]);
        $form = $this->createForm(GameType::class, $gameEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($form->getData());
            $this->em->flush();
            return $this->redirectToRoute('app_admin_game');
        }

        return $this->render('admin_game/modify.html.twig', [
            'form' => $form->createView(),
            'game' => $gameEntity
        ]);
    }

    #[Route('/admin/game/supprimer/{id}', name: 'app_admin_game_delete')]
    public function delete(int $id): Response
    {
        $gameEntity = $this->gameRepository->find($id);
        $this->em->remove($gameEntity);
        $this->em->flush();

        return $this->redirectToRoute('app_admin_game');
    }

}
