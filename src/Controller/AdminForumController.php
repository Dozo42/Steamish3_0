<?php

namespace App\Controller;

use App\Entity\Forum;
use App\Form\ForumSearchType;
use App\Form\ForumType;
use App\Repository\ForumRepository;
use App\Repository\TopicRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminForumController extends AbstractController
{
    public function __construct(
        private ForumRepository $forumRepository,
        private TopicRepository $topicRepository,
        private EntityManagerInterface $em,
        private PaginatorInterface $paginator
    )
    {
        
    }

    #[Route('/admin/forum', name: 'app_admin_forum')]
    public function index(Request $request): Response
    {
        $qb = $this->forumRepository->getQbAll();

        $form = $this->createForm(ForumSearchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $qb = $this->forumRepository->updateQbByData($qb, $form->getData());
        }

        $pagination = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin_forum/index.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/forum/detail/{id}', name: 'app_admin_forum_detail')]
    public function show(Request $request, int $id): Response
    {
        $forumEntity = $this->forumRepository->find($id);
        $qb = $this->topicRepository->getQbAll($id);
        $pagination = $this->paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin_forum/show.html.twig', [
            'forum' => $forumEntity,
            'pagination' => $pagination
        ]);
    }

    #[Route('/admin/forum/ajouter', name: 'app_admin_forum_add')]
    public function add(Request $request): Response
    {
        $forumEntity = new Forum();
        $form = $this->createForm(ForumType::class, $forumEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $forumEntity->setCreatedAt(new DateTime());
            $this->em->persist($form->getData());
            $this->em->flush();
            return $this->redirectToRoute('app_admin_forum');
        }

        return $this->render('admin_forum/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/forum/modifier/{id}', name: 'app_admin_forum_modify')]
    public function modify(Request $request, int $id): Response
    {
        $forumEntity = $this->forumRepository->find($id);
        $form = $this->createForm(ForumType::class, $forumEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($form->getData());
            $this->em->flush();
            return $this->redirectToRoute('app_admin_forum');
        }

        return $this->render('admin_forum/modify.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/forum/supprimer/{id}', name: 'app_admin_forum_delete')]
    public function delete(int $id): Response
    {
        $forumEntity = $this->forumRepository->find($id);
        $this->em->remove($forumEntity);
        $this->em->flush();

        return $this->redirectToRoute('app_admin_forum');
    }

}
