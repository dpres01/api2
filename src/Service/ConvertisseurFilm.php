<?php

namespace App\Service;

use App\Entity\Film;


class ConvertisseurFilm
{
     
    /**
     * Modification d'un film.
     *
     * @param $donnees.
     * @param Film $film.
     *
     * @return array 
     *
     */
	public function convert($donnees, Film $film): ?array
	{
		if(empty($donnees->getTitre()) && empty($donnees->getDescription()) 
	       && empty($donnees->getImage()) && empty($donnees->getDateCreation())
	       && empty($donnees->getCategorie()) ) 
			return null; 

		$retour = array("idcat" => null);

		if ($donnees->getTitre())
			$film->setTitre($donnees->getTitre());
		if ($donnees->getDescription()) 
			$film->setDescription($donnees->getDescription());
		if($donnees->getImage()) 
		    $film->setImage($donnees->getImage());
		if ($donnees->getDateCreation())
		    $film->setDateCreation($donnees->getDateCreation());
		if ($donnees->getCategorie()) 
			    $retour["idcat"] = $donnees->getCategorie()->getId();
        $retour["film"] = $film;
		return $retour;
	}
}