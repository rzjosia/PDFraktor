<?php

namespace App\Repository;

use App\Entity\PdfDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PdfDocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method PdfDocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method PdfDocument[]    findAll()
 * @method PdfDocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PdfDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PdfDocument::class);
    }
    
    // /**
    //  * @return PdfDocument[] Returns an array of PdfDocument objects
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
    public function findOneBySomeField($value): ?PdfDocument
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
