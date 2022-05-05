<?php

namespace App\Controller;

use App\Repository\PublisherRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/editeur')]
class PublisherController extends AbstractController
{

    public function __construct(PublisherRepository $publisherRepository)
    {
        $this->publisherRepository =$publisherRepository;
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
        return $this->render('publisher/showPublisher.html.twig', [
            'publisher' => $publisherEntity,
        ]);
    }
}