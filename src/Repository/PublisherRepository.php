<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Publisher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Publisher|null find($id, $lockMode = null, $lockVersion = null)
 * @method Publisher|null findOneBy(array $criteria, array $orderBy = null)
 * @method Publisher[]    findAll()
 * @method Publisher[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublisherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Publisher::class);
    }

    public function add(Publisher $publisher, bool $flush = true): void {
        $this->_em->persist($publisher);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(Publisher $publisher, bool $flush = true): void {
        $this->_em->remove($publisher);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getQueryBuilder(): QueryBuilder {
        return $this->createQueryBuilder('p')
            ->select('p', 'country', 'games')
            ->join('p.country', 'country')
            ->join('p.games', 'games')
        ;
    }

    /**
     * @return Publisher|null
     */
    public function getPublisherBySlug(string $slug): ?Publisher {
        return $this->getQueryBuilder()
            ->where('p.slug = :slug')
            ->setParameter('slug', $slug)
            ->orderBy('games.publishedAt', 'DESC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return Publisher[]
     */
    public function getPublishersAll(): array {
        return $this->getQueryBuilder()
            ->orderBy('p.name')
//            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getPublishersByAjaxRequest(string $research) {

        return $this->createQueryBuilder('p')
        ->where('p.name LIKE :research')
        ->setParameter('research' , '%'.$research.'%')
        ->getQuery()
        ->getResult()
        ;
    }

    public function getQbAll() {
        return $this->createQueryBuilder('g');
    }

    public function updateQbByData($qb, $data) {

        if(isset($data['name']) &&  $data['name']){
            $qb->andWhere('g.name LIKE :myname')
            ->setParameter('myname', '%'.$data['name'].'%');
        }
        if(isset($data['createdAt']) &&  $data['createdAt']){
            $qb->andWhere('g.createdAt > :mydate')
            ->setParameter('mydate',  $data['createdAt']);
        }

        return $qb;
    }


}
