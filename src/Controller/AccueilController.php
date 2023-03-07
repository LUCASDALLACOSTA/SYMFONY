<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    /**
     * @Route("/")
     */
    function bonjour() {
        return $this->render('accueil.html.twig',[
        ]);
    }

    /**
     * @Route("/produits/{var}")
     */
    function afficheCreneau($var){
        $commentaires = [
            'Je ne serai pas disponible sur cette période (Gautier)',
            'test 2',
            "test'test",
        ];
        return $this->render('creneau/affiche.html.twig', [
            'title'=>ucwords(str_replace('-',' ',$var)),
            'commentaires'=>$commentaires,
            ]);
    }
}