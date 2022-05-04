<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Genre;
use App\Entity\Library;
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
 }