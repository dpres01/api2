<?php

namespace App\Repository;

use App\Entity\Film;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

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

     /**
      * recherche filme par : categorie,titre,date_creation 
      *
      * @param $categorie      
      * @param $titre.
      * @param $date.
      *
      * @return  array 
      */
    
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
            $qb->andWhere($qb->expr()->eq('f.date_creation', ':date'));
            $qb->setParameter(':date', $date); 
        }
        return $qb->getQuery()->getResult();
    }
    
    
     /**
      * recherche filme par page suivant categorie X 
      *
      * @param $page      
      * @param $maxResult
      * @param $categorie
      *
      * @return  array 
      */
    public function findFilmByCategory($page,$maxResult,$categorie): array
    {   
        
        if ( empty($page) || $page < 1 ){
            return array(
                array("error" => "page n'existe pas"),
                "status" => Response::HTTP_BAD_REQUEST
            );
        }

        if ( empty($maxResult) || $maxResult < 1 ) {
            return array(
                array("error" => "max n'existe pas ou inferieur à page "), 
                "status" => Response::HTTP_BAD_REQUEST
            );
        }
        
        $nbreLigne = $this->compterFilmCategorie($categorie);
        
        if($nbreLigne < $maxResult){
            return array(
                array("error" => "nombre de ligne ne doit pas être < au max.page"),
                "status" => Response::HTTP_BAD_REQUEST
            );   
        }

        $nbrePage = intval(ceil($nbreLigne/$maxResult));

        $qb = $this->createQueryBuilder('f')
            ->andWhere('f.categorie = :cat')
            ->setParameter('cat', $categorie);

        $debut = (intval($page) - 1) * intval($maxResult);
        $qb->setFirstResult($debut)->setMaxResults(intval($maxResult));     
           
        $result = $qb->getQuery()->getResult();
        
         
        return array(
                    array(
                        "films" => $result,
                        "nbre_page" => $nbrePage,
                        "page_actuelle"=>$page
                    ),
                   "status" => Response::HTTP_OK
                );
    }

    public function compterFilmCategorie($categorie){
        return $this->createQueryBuilder('f')
            ->select('count(f.id)')
            ->andWhere('f.categorie = :cat')
            ->setParameter('cat', $categorie)
            ->getQuery() ->getSingleScalarResult();
    }
    
}
