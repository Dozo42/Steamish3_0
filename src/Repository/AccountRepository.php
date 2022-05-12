<?php

namespace App\Repository;

use App\Entity\Account;
use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Account|null find($id, $lockMode = null, $lockVersion = null)
 * @method Account|null findOneBy(array $criteria, array $orderBy = null)
 * @method Account[]    findAll()
 * @method Account[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    public function add(Account $account, bool $flush = true): void {
        $this->_em->persist($account);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(Account $account, bool $flush = true): void {
        $this->_em->remove($account);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getAccountAllDetails(string $name): ?Account {

        return $this->createQueryBuilder('a')
        ->select('a', 'lib', 'games')
        ->leftJoin('a.libraries', 'lib')
        ->leftJoin('lib.game', 'games')
        ->where('a.name = :name')
        ->setParameter('name', $name)
        ->getQuery()
        ->getOneOrNullResult()
        ;
    }

    public function getAccountsPlayTime(): array
    {
        return $this->createQueryBuilder('t')
        // acount.*, 
        ->select('t', 'SUM(lib.gameTime)')
        ->leftJoin('t.libraries', 'lib')
        ->groupBy('t')
        ->orderBy('SUM(lib.gameTime)', 'DESC')
        ->getQuery()
        ->getResult()[0]
        ;
    }

    public function getMostActiveUser(){
        return $this->createQueryBuilder('u')
            ->join(Message::class, 'm', Join::WITH, 'm.createdBy = u')
            ->groupBy('m.createdBy')
            ->orderBy('COUNT(m)', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult()
            ;
    }

    public function getQbAll()
    {
        return $this->createQueryBuilder('a');
        
    }

}
