<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Game;
use App\Entity\Genre;
use App\Entity\Library;
use App\Entity\Publisher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function add(Game $game, bool $flush = true): void {
        $this->_em->persist($game);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(Game $game, bool $flush = true): void {
        $this->_em->remove($game);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getGamesByDate() {
        return $this->createQueryBuilder('g')
        ->orderBy('g.publishedAt', 'desc')
        ->setMaxResults(9)
        ->getQuery()->getResult();
    }

    public function getMostPlayedGames(int $limit = 9) {
        /* SELECT game.*, SUM(library.game_time) FROM `game` 
        JOIN library ON library.game_id = game.id
        GROUP BY game.id
        ORDER BY SUM(library.game_time) DESC
        LIMIT 9 */

        return $this->createQueryBuilder('g')
            ->join(Library::class, 'lib', Join::WITH, 'lib.game = g')
            ->groupBy('g.name')
            ->orderBy('SUM(lib.gameTime)', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;

    }

    public function getMostBoughtGames(int $limit = 9) {

        return $this->createQueryBuilder('g')
            ->join(Library::class, 'lib', Join::WITH, 'lib.game = g')
            ->groupBy('g.name')
            ->orderBy('COUNT(lib.game)', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getGameAllDetails(string $slug): ?Game {
        return $this->createQueryBuilder('g')
        ->select('g', 'c', 'ge', 'co', 'p', 'acc')
        ->join('g.countries', 'c')
        ->join('g.genres', 'ge')
        ->leftJoin('g.comments', 'co')
        ->leftJoin('co.account', 'acc')
        ->leftJoin('g.publisher', 'p')
        ->where('g.slug = :slug')
        ->setParameter('slug', $slug)
        ->orderBy('co.createdAt', 'DESC')
        ->getQuery()
        ->getOneOrNullResult()
    ;
    }

    public function getSimilarGames(Game $game){
        return $this->createQueryBuilder('g')
        ->select('g')
        ->join('g.genres', 'ge')
        ->where('ge IN (:genres)')
        ->setParameter('genres', $game->getGenres())
        ->andWhere('g != :game')
        ->setParameter('game', $game)
        ->orderBy('g.publishedAt', 'DESC')
        ->getQuery()
        ->getResult()
        ;
    }

    public function getCountBoughtGamesByPublisher(Publisher $publisher) {

        return $this->createQueryBuilder('g')
            ->select('g', 'COUNT(g)')
            ->join(Library::class, 'lib', Join::WITH, 'lib.game = g')
            ->leftJoin('g.publisher', 'p')
            ->where('p = :publisher')
            ->setParameter('publisher',$publisher)
            ->groupBy('lib.game')
            ->getQuery()
            ->getResult()
        ;
    }


 }