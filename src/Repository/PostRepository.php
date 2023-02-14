<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Post $entity, bool $flush = true): void
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
    public function remove(Post $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    // find all posts by array of departments
    public function findByDepartments($departments)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->join('p.departments', 'd')
            ->where('d IN (:departments)')
            ->setParameter('departments', $departments);
        return $qb->getQuery()->getResult();
    }
    // find all posts that are announcements by array of departments
    public function findAnnouncementsByDepartments($departments)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->join('p.departments', 'd')
            ->join('p.announcement', 'a')
            ->where('d IN (:departments)')
            ->setParameter('departments', $departments)
            ->orderBy('p.id', 'DESC');
        return $qb->getQuery()->getResult();
    }
    // find all posts that announcement is not null and reverse order
    public function findAnnouncements()
    {
        $qb = $this->createQueryBuilder('p');
        $qb->join('p.announcement', 'a')
            ->orderBy('p.id', 'DESC');
        return $qb->getQuery()->getResult();
    }

    // find all posts that workshop in workPost in engagementPost is not null and reverse order
    public function findWorkshops()
    {
        $qb = $this->createQueryBuilder('p');
        $qb->join('p.engagementPost', 'e')
            ->join('e.workPost', 'wp')
            ->join('wp.workshop', 'ws')
            ->orderBy('p.id', 'DESC');
        return $qb->getQuery()->getResult();
    }

    // find all posts by array departments that workshop in workPost in engagementPost is not null and reverse order
    public function findWorkshopsByDepartments($departments)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->join('p.departments', 'd')
            ->join('p.engagementPost', 'e')
            ->join('e.workPost', 'wp')
            ->join('wp.workshop', 'ws')
            ->where('d IN (:departments)')
            ->setParameter('departments', $departments)
            ->orderBy('p.id', 'DESC');
        return $qb->getQuery()->getResult();
    }

    public function findMeetingsByDepartments($departments)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->join('p.departments', 'd')
            ->join('p.engagementPost', 'e')
            ->join('e.workPost', 'wp')
            ->join('wp.meeting', 'm')
            ->where('d IN (:departments)')
            ->setParameter('departments', $departments)
            ->orderBy('p.id', 'DESC');
        return $qb->getQuery()->getResult();
    }
    public function findMeetings()
    {
        $qb = $this->createQueryBuilder('p');
        $qb->join('p.engagementPost', 'e')
            ->join('e.workPost', 'wp')
            ->join('wp.meeting', 'm')
            ->orderBy('p.id', 'DESC');
        return $qb->getQuery()->getResult();
    }
    public function findTrainings()
    {
        $qb = $this->createQueryBuilder('p');
        $qb->join('p.engagementPost', 'e')
            ->join('e.training', 't')
            ->orderBy('p.id', 'DESC');
        return $qb->getQuery()->getResult();
    }

    public function findTrainingsByDepartments($departments)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->join('p.departments', 'd')
            ->join('p.engagementPost', 'e')
            ->join('e.training', 't')
            ->where('d IN (:departments)')
            ->setParameter('departments', $departments)
            ->orderBy('p.id', 'DESC');
        return $qb->getQuery()->getResult();
    }

    // find polls
    public function findPolls()
    {
        $qb = $this->createQueryBuilder('p');
        $qb->join('p.poll', 'pl')
            ->orderBy('p.id', 'DESC');
        return $qb->getQuery()->getResult();
    }
    // find polls by departments
    public function findPollsByDepartments($departments)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->join('p.departments', 'd')
            ->join('p.poll', 'pl')
            ->where('d IN (:departments)')
            ->setParameter('departments', $departments)
            ->orderBy('p.id', 'DESC');
        return $qb->getQuery()->getResult();
    }

}
