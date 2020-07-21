<?php

namespace App\Repository;

use App\Entity\Film;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Film|null find($id, $lockMode = null, $lockVersion = null)
 * @method Film|null findOneBy(array $criteria, array $orderBy = null)
 * @method Film[]    findAll()
 * @method Film[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FilmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Film::class);
    }

    // /**
    //  * @return Film[] Returns an array of Film objects
    //  */
    
    public function rechercheFilm($categorie,$titre,$date): array
    {
        $qb= $this->createQueryBuilder('f');
           
        if (! empty($categorie)) {
            $qb->andWhere($qb->expr()->eq('f.categorie', ':cat'));
            $qb->setParameter(':cat', (int)$categorie); 
        }

        if (! empty($titre) && is_string($titre)) {
            $qb->andWhere($qb->expr()->eq('f.titre', ':titre'));
            $qb->setParameter(':titre', $titre); 
        }

        if (! empty($date) ) {
            //$date = new \DateTime($date);
            //$date->format("Y-m-d");
            $qb->andWhere($qb->expr()->eq('f.date_creation', ':date'));
            $qb->setParameter(':date', $date); 
        }
        return $qb->getQuery()->getResult();
    }
    

    public function findFilmByCategory($page,$maxResult,$categorie): array
    {   
        if ( empty($page) || is_int($page) || $page < 1 ){
            return array("error" => "page n'existe pas");
        }

        if ( (empty($maxResult) || is_int($maxResult) || $maxResult < 1) && $maxResult < $page){
            return array("error" => "max n'existe pas ou inferieur à page ");
        }
        
        if ( emty($categorie) ) return array("error"=>"la categorie ne doit pas être vide");

        $this->createQueryBuilder('f')
            ->andWhere('f.categorie = :cat')
            ->setParameter('cat', $categorie);

        $pageActuelle = (intval($page) - 1) * intval($maxResult);
        $qb->setFirstResult($pageActuelle)->setMaxResults(intval($maxResult));     
           
        $result = $qb->getQuery()->getOneOrNullResult();
        $nbrePage = $this->compterFilmCategorie($categorie);

        return array("reslutat_req" => $result,"nbre_page" => $nbrePage);
    }

    public function compterFilmCategorie($categorie){
        return $this->createQueryBuilder('count(f)')
            ->andWhere('f.categorie = :cat')
            ->setParameter('cat', $categorie)
            ->getQuery() ->getSingleScalarResult();
    }
    
}
