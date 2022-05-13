<?php

namespace App\Repository;

use App\Entity\Genre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Genre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Genre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Genre[]    findAll()
 * @method Genre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GenreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Genre::class);
    }

    public function add(Genre $genre, bool $flush = true): void {
        $this->_em->persist($genre);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(Genre $genre, bool $flush = true): void {
        $this->_em->remove($genre);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getGamesByGenre(string $slug) {
        return $this->createQueryBuilder('genre')
        ->select('genre', 'game')
        ->join('genre.games', 'game')
        ->where('genre.slug = :slug')
        ->setParameter('slug', $slug)
        ->getQuery()
        ->getSingleResult()
        ;
    }

    public function getGenresByAjaxRequest(string $research) {

        return $this->createQueryBuilder('g')
        ->where('g.name LIKE :research')
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

        return $qb;
    }
}
