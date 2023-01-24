<?php

namespace App\Repository;

use App\Entity\PdfUrl;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PdfUrl|null find($id, $lockMode = null, $lockVersion = null)
 * @method PdfUrl|null findOneBy(array $criteria, array $orderBy = null)
 * @method PdfUrl[]    findAll()
 * @method PdfUrl[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PdfUrlRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, PdfUrl::class);
    }
    
    /**
     * @param $name
     */
    public function findByPathQuery($name) : Query
    {
        return $this->createQueryBuilder("u")
            ->where("u.path = :name")
            ->setParameter('name', $name)
            ->getQuery();
    }
    
    
    
    // /**
    //  * @return PdfUrl[] Returns an array of PdfUrl objects
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
    public function findOneBySomeField($value): ?PdfUrl
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
