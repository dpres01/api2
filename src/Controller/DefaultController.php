<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Service\EnregistreurFilm;
use App\Service\ConvertisseurFilm;
use App\Entity\Film;
use App\Entity\Categorie;

/**
 * @Route("/api/films")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/liste/{id}", name="lister_films", methods="GET")
     */
    public function listerFilm(Request $request): JsonResponse
    {   
        $em = $this->getDoctrine()->getManager();
        $retour = $em->getRepository(Film::class)
                    ->findFilmByCategory($request->query->get("page"),
                        $request->query->get("nbreMax"),$categorie =null);
        return $this->json($retour);
    }
    
    /**
     * Creation d'un nouveau film
     *
     * @Route("/ajout", name="ajouter_film", methods="POST")
     *
     * @param SerializerInterface $serializer service des serialisation d'objet      
     * @param ValidatorInterface $validator.
     *
     * @return JsonResponse
     */
    public function creationFilm(
        Request $request, 
        SerializerInterface $serializer,
        EnregistreurFilm $enregistreur
    ): JsonResponse
    {   
        $film = $serializer->deserialize($request->getContent(),Film::class,'json');
        $retour = $enregistreur->enregistre($film);
        return $this->json($retour[0],$retour["status"]);
    }

    /**
     * Modifie un film à partir de données envoyées
     *
     * @Route("/modifier/{id}", name="modifier_film", methods="PUT")
     *
     * @param Request $request      
     * @param Film $film.
     * @param SerializerInterface $serializer service des serialisation d'objet
     * @param ConvertisseurFilm $converteur
     * @param EnregistreurFilm $enregistreur
     *
     * @return JsonResponse
     */
    public function modificationFilm(
        Request $request,
        /*Film */$film,
        SerializerInterface $serializer,
        ConvertisseurFilm $converteur,
        EnregistreurFilm $enregistreur
    ): JsonResponse
    {
        
        $donnees = $serializer->deserialize($request->getContent(),Film::class,'json');
        $convert = $converteur->convert($donnees,$film);
        if (! empty($convert)) {
             $retour = $enregistreur->enregistre($convert["film"],$convert["idcat"],true);
            return $this->json($retour[0],$retour["status"]);
        }
        return $this->json(['message' => 'rien a modifier',]);
    }

    /**
     * @Route("/supprimer/{id}", name="supprimer_film", methods="DELETE")
     *
     */
    public function supressionFilm(Film $film): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($film);
        $em->flush();
        return new JsonResponse(["message"=>"Un film a été supprimé " ],Response::HTTP_CREATED); 
    }

    /**
     * Affichage d'un film
     *
     * @Route("/film/{id}", name="aficher_film", methods="GET")
     *
     * @param Film $film.
     *
     * @return JsonResponse
     */
    public function affichageFilm(Film $film): JsonResponse
    {
        return $this->json($film);
    }

    /**
     * recherche des films depuis un terme
     *
     * @Route("/search", name="recherche_film", methods="GET")
     *
     * @param Film $film.
     *
     * @return JsonResponse
     */
    public function rechercheFilm(Request $request): JsonResponse
    {  
        $em = $this->getDoctrine()->getManager();
        $retour = $em->getRepository(Film::class)->rechercheFilm(
            $request->query->get("categorie"),
            $request->query->get("titre"),
            $request->query->get("date")
        );
        return $this->json($retour);
    }
    
}
