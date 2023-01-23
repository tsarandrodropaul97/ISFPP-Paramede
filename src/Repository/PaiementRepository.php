<?php

namespace App\Repository;

use App\Entity\Paiement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Paiement>
 *
 * @method Paiement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Paiement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Paiement[]    findAll()
 * @method Paiement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaiementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paiement::class);
    }

    public function add(Paiement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Paiement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findDistinctEtudiant($valeur): array
    {
        return $this->createQueryBuilder('p')
            ->select('MAX(p.payer) AS dernierPaiment,
                     p.id as idPaiement,
                     n.id as idNiveau, n.nom as niveau,
                     e.id as idEtudiant, e.nom as nomEtudiant, e.prenom as prenomEtudiant, e.matricule,
                     f.id as idFrais')
            ->join('p.niveau', 'n')
            ->join('p.etudiant', 'e')
            ->join('p.frais', 'f')
            ->andWhere('p.niveau = :val')
            ->groupBy('p.etudiant')
            ->setParameter('val', $valeur)
            ->getQuery()
            ->getResult();;
    }


    public function findEcolageAvance($valeur, $niveau): array
    {
        return $this->createQueryBuilder('p')
            ->select('MAX(p.payer) AS dernierPaiment')
            ->andWhere('p.payer >= :val and p.niveau = :niv')
            ->groupBy('p.etudiant')
            ->setParameter('val', $valeur)
            ->setParameter('niv', $niveau)
            ->getQuery()
            ->getResult();
    }

    public function findEcolage($niveau): array
    {
        return $this->createQueryBuilder('p')
            ->select('MAX(p.payer) AS dernierPaiment , p as etudiant')
            ->andWhere('p.niveau = :niv')
            ->groupBy('p.etudiant')
            ->setParameter('niv', $niveau)
            ->getQuery()
            ->getResult();
    }

    public function findDernierPaiment($niveau, $etudiant): array
    {
        return $this->createQueryBuilder('p')
            ->select('MAX(p.payer) AS dernierPaiment')
            ->andWhere('p.niveau = :niv and p.etudiant = :et')
            ->setParameter('niv', $niveau)
            ->setParameter('et', $etudiant)
            ->getQuery()
            ->getResult();
    }

    public function recetteParMoi($moi, $annee): array
    {
        return $this->createQueryBuilder('p')
            ->select("sum(f.ecolage) as somme")
            ->join('p.frais', 'f')
            ->andWhere('month(p.payer) = :val and YEAR(p.payer) = :an')
            ->setParameter('val', $moi)
            ->setParameter('an', $annee)
            ->getQuery()
            ->getResult();
    }

    public function recetteParAnnee($moi, $annee): array
    {
        $query = $this->createQueryBuilder('p');

        if ($annee->getAn()) {
            $query = $query->select("sum(f.ecolage) as somme, sum(f.droit) as sommeDroit");
            $query = $query->join('p.frais', 'f');
            $query = $query->andWhere("month(p.payer) = :val and YEAR(p.payer) = :an");
            $query = $query->setParameter('val', $moi);
            $query = $query->setParameter('an', $annee->getAn());
        }
        return $query->getQuery()->getResult();
    }

    public function paiementParEtudiant($etudiant): array
    {
        return $this->createQueryBuilder('p')
            ->select("DATE_FORMAT(p.payer, '%d-%m-%Y') as ecolagePayer, p.datePaiment as datePaiement")
            ->andWhere('p.etudiant = :val')
            ->setParameter('val', $etudiant)
            ->getQuery()
            ->getResult();
    }


    public function dernierPaiment($etudiant): array
    {
        return $this->createQueryBuilder('p')
            ->select("MAX(DATE_FORMAT(p.payer, '%d-%m-%Y')) AS dernierPaiment")
            ->andWhere('p.etudiant = :et')
            ->setParameter('et', $etudiant)
            ->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return Paiement[] Returns an array of Paiement objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Paiement
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
