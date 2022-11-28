<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccueilController extends AbstractController
{

    function bonjour() {
        return new Response( content: 'Voici la page princiale de DALLA COSTA Lucas');
    }

    /**
     * @Route("/produits/{var}")
     */
    function afficheCreneau($var) {
        $commentaires = [
            'Je ne serai pas disponible sur cette periode (Gautier)',
            'Je veux bien assurer la relève (Sophie)',
            "Pensez à reporter l'heure manquante (Mélanie)",
            ];

        return $this->render('creneau/affiche.html.twig',[
            'title'=>ucwords(str_replace('-',' ',$var)),
            'commentaires'=> $commentaires,
            ]);
    }
}