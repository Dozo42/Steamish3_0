<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Form\GenreType;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminGenreController extends AbstractController
{
    public function __construct(
        private GenreRepository $genreRepository,
        private EntityManagerInterface $em
        )
    {
        
    }

    #[Route('/admin/genre', name: 'app_admin_genre')]
    public function index(): Response
    {
        return $this->render('admin_genre/index.html.twig', [
            'genres' => $this->genreRepository->findAll()
        ]);
    }

    #[Route('/admin/genre/detail/{slug}', name: 'app_admin_genre_detail')]
    public function show(string $slug): Response
    {
        return $this->render('admin_genre/show.html.twig', [
            'genre' => $this->genreRepository->getGamesByGenre($slug)
        ]);
    }

    #[Route('/admin/genre/ajouter', name: 'app_admin_genre_add')]
    public function add(Request $request): Response
    {
        $genreEntity = new Genre();
        $form = $this->createForm(GenreType::class, $genreEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($form->getData());
            $this->em->flush();
            return $this->redirectToRoute('app_admin_genre');
        }

        return $this->render('admin_genre/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/genre/modifier/{slug}', name: 'app_admin_genre_modify')]
    public function modify(Genre $genre, string $slug, Request $request): Response
    {
        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($form->getData());
            $this->em->flush();
            return $this->redirectToRoute('app_admin_genre');
        }

        return $this->render('admin_genre/modify.html.twig', [
            'form' => $form->createView(),
            'genre' => $this->genreRepository->findOneBy(['slug' => $slug])
        ]);
    }

    #[Route('/admin/genre/supprimer/{id}', name: 'app_admin_genre_delete')]
    public function delete(int $id): Response
    {
        $genreEntity = $this->genreRepository->find($id);
        $this->em->remove($genreEntity);
        $this->em->flush();

        return $this->redirectToRoute('app_admin_genre');
    }
}
