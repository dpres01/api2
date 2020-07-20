<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use App\Service\EnregistreurFilm;
use App\Entity\Film;

/**
 * @Route("/api/films")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="lister_films", methods="GET")
     */
    public function listerFilm(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/DefaultController.php',
        ]);
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
        EnregistreurFilm $enregistreur): JsonResponse
    {   
        $film = $serializer->deserialize($request->getContent(),Film::class,'json');
        $retour = $enregistreur->enregistre($film);
        return $this->json($retour[0],$retour["status"]);
    }

    /**
     * @Route("/modifier/{id}", name="modifier_film", methods="UPDATE")
     */
    public function modificationFilm(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/DefaultController.php',
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="supprimer_film", methods="DELETE")
     */
    public function supressionFilm(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/DefaultController.php',
        ]);
    }
}
