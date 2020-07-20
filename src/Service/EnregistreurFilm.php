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
	
	public function enregistre(Film $film)
	{	
		$errors = $this->validator->validate($film);
        if (count($errors) > 0) {
        	foreach ($errors as $error) {
        		$messages[] = $error->getMessage();
        	}
            return array(array("errors"=>$messages),"status"=>Response::HTTP_BAD_REQUEST);
        }

		if ($id = $film->getCategorie()->getId()) {
			$categorie = $this->em->getRepository(Categorie::class)->find($id);
			$film->setCategorie($categorie);
		}

		try {
			$this->em->persist($film);
		    $this->em->flush();
		    return array(array("message"=>"ok"),"status"=>Response::HTTP_CREATED);
		} catch (\Exception $e) {
			return array(array("error"=>$e),"status"=>Response::HTTP_INTERNAL_SERVER_ERROR);
		}
		
	}
}