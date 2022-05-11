<?php

namespace App\Controller;

use App\Entity\Publisher;
use App\Form\PublisherType;
use App\Repository\PublisherRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPublisherController extends AbstractController
{
    public function __construct(
        private PublisherRepository $publisherRepository,
        private EntityManagerInterface $em,
        private PaginatorInterface $paginator,
        )
    {
        
    }

    #[Route('/admin/editeur', name: 'app_admin_publisher')]
    public function index(Request $request): Response
    {
        $qb = $this->publisherRepository->getQbAll();
        $pagination = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('admin_publisher/index.html.twig', [
            'pagination' => $pagination
        ]);
        // return $this->render('admin_publisher/index.html.twig', [
        //     'publishers' => $this->publisherRepository->findAll()
        // ]);
    }

    #[Route('/admin/editeur/detail/{slug}', name: 'app_admin_publisher_detail')]
    public function show($slug): Response
    {
        return $this->render('admin_publisher/show.html.twig', [
            'publisher' => $this->publisherRepository->findOneBy(['slug' => $slug])
        ]);
    }

    #[Route('/admin/editeur/ajouter', name: 'app_admin_publisher_add')]
    public function add(Request $request): Response
    {
        $publisherEntity = new Publisher();
        $form = $this->createForm(PublisherType::class, $publisherEntity);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $publisherEntity->setCreatedAt(new DateTime());
            $this->em->persist($form->getData());
            $this->em->flush();
            return $this->redirectToRoute('app_admin_publisher');
        }

        return $this->render('admin_publisher/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/editeur/modifier/{slug}', name: 'app_admin_publisher_modify')]
    public function modify(string $slug, Request $request): Response
    {
        $publisherEntity = $this->publisherRepository->findOneBy(['slug' => $slug]);
        $form = $this->createForm(PublisherType::class, $publisherEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($form->getData());
            $this->em->flush();
            return $this->redirectToRoute('app_admin_publisher');
        }

        return $this->render('admin_publisher/modify.html.twig', [
            'form' => $form->createView(),
            'publisher' => $publisherEntity
        ]);
    }

    #[Route('/admin/editeur/supprimer/{id}', name: 'app_admin_publisher_delete')]
    public function delete(int $id): Response
    {
        $publisherEntity = $this->publisherRepository->find($id);
        $this->em->remove($publisherEntity);
        $this->em->flush();

        return $this->redirectToRoute('app_admin_publisher');
    }
}
