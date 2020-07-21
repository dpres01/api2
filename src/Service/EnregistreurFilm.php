<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Film;
use App\Entity\Categorie;

class EnregistreurFilm{
     
    private $em;
    private $validator;

	public function __construct(EntityManagerInterface $em, ValidatorInterface $validator)
	{
	    $this->em = $em; 
	    $this->validator = $validator; 
	}
	
	/**
     * Enregistre un film àpres validation.
     *
     * @param Film $film.
     * @return array 
     *
     */
	public function enregistre(Film $film, $idcat = null, $modif = false): array
	{	
		$errors = $this->validator->validate($film);
        if (count($errors) > 0) {
        	foreach ($errors as $error) {
        		$messages[] = $error->getMessage();
        	}
            return array(array("errors" => $messages),"status" => Response::HTTP_BAD_REQUEST);
        }

        $id =  $modif ? $idcat : $film->getCategorie()->getId();

		if ($id) {
			$categorie = $this->em->getRepository(Categorie::class)->find($id);
			$film->setCategorie($categorie);
		}
        
		try {
			if (! $modif) {
			    $this->em->persist($film);
			    $message = array("message" => "Le film a été bien créé");
			}else{
			    $message = array("message" => "Le film a été bien modifié");	
			}
		    $this->em->flush();
		    return array($message,"status" => Response::HTTP_CREATED);
		} catch (\Exception $e) {
			return array(array("error" => $e),"status" => Response::HTTP_INTERNAL_SERVER_ERROR);
		}
		
	}
}