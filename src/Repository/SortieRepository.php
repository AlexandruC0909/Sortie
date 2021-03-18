<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
    public function changerStatutSortie($etat,$id)
    {
        $dql = <<<DQL
UPDATE App\Entity\Sortie s
SET
    s.etat =:etat
 
WHERE s.id =:id

DQL;


        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery($dql);
        $query ->setParameter(':etat',$etat);
        $query ->setParameter(':id',$id);

        return $query->getResult();
    }

    public function search($nom, $sites,$organisateurId,$idParticipant,$notParticipantId,$dateInf,$dateSup){
        $query = $this->createQueryBuilder('s');
        $query->leftJoin('s.organizateur', 'p');
        $query->leftJoin('p.site', 'l');
        $query->leftJoin('s.listeParticipants', 'q');

        if ($nom!=null){
            $query->andWhere('s.nom LIKE :nom');
            $query->setParameter('nom', '%'.$nom.'%');
        }
        if ($sites!=null){
            $query->andWhere('l.id = :idSite');
            $query ->setParameter(':idSite',$sites);

        }
        if ($organisateurId!=null){
            $query->andWhere('p.id = :idOrganisateur');
            $query ->setParameter(':idOrganisateur',$organisateurId);

        }
        if ($idParticipant!=null){

            $query->andWhere('q.id = :idParticipant');
            $query ->setParameter(':idParticipant',$idParticipant);

        }
        if ($notParticipantId!=null){

            $query->andWhere('q.id <> :idNotParticipant');
            $query ->setParameter(':idNotParticipant',$notParticipantId);

        }
        if ($dateInf!=null){
            $query->andWhere('s.dateHeureDebut > :dateInf');
            $query ->setParameter(':dateInf',$dateInf);

        }
        if ($dateSup!=null){
            $query->andWhere('s.dateHeureDebut < :dateSup');
            $query ->setParameter(':dateSup',$dateSup);

        }
        return $query->getQuery()->getResult();

    }
    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
