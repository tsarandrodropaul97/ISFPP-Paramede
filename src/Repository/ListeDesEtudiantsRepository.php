<?php

namespace App\Repository;

use App\Entity\ListeDesEtudiants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ListeDesEtudiants>
 *
 * @method ListeDesEtudiants|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListeDesEtudiants|null findOneBy(array $criteria, array $orderBy = null)
 * @method ListeDesEtudiants[]    findAll()
 * @method ListeDesEtudiants[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ListeDesEtudiantsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListeDesEtudiants::class);
    }

    public function add(ListeDesEtudiants $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ListeDesEtudiants $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ListeDesEtudiants[] Returns an array of ListeDesEtudiants objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ListeDesEtudiants
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
