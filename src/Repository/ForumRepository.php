<?php

namespace App\Repository;

use App\Entity\Forum;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Forum>
 *
 * @method Forum|null find($id, $lockMode = null, $lockVersion = null)
 * @method Forum|null findOneBy(array $criteria, array $orderBy = null)
 * @method Forum[]    findAll()
 * @method Forum[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Forum::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Forum $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Forum $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getForumTendance() {
        return $this->createQueryBuilder('f')
            ->join('f.topics', 't')
            ->join('t.messages', 'm')
            ->groupBy('f.title')
            ->where('m.createdAt < :today')
            ->andWhere('m.createdAt > :oneWeek')
            ->orderBy('COUNT(m)', 'DESC')
            ->setParameter('today', new DateTime())
            ->setParameter('oneWeek', new DateTime('-1 week'))
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult()
            ;
    }

    public function getForumGold() {
        return $this->createQueryBuilder('f')
            ->join('f.topics', 't')
            ->join('t.messages', 'm')
            ->groupBy('f.title')
            ->orderBy('COUNT(m)', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult()
            ;
    }

    public function updateQbByData($qb, $data) {

        if(isset($data['title']) &&  $data['title']){
            $qb->andWhere('f.title LIKE :mavar')
            ->setParameter('mavar', '%'.$data['title'].'%');
        }
        if(isset($data['createdAt']) &&  $data['createdAt']){
            $qb->andWhere('f.createdAt > :mavar')
            ->setParameter('mavar',  $data['createdAt']);
        }

        return $qb;
    }


    // /**
    //  * @return Forum[] Returns an array of Forum objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Forum
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getQbAll() {
        return $this->createQueryBuilder('f');
    }
}
