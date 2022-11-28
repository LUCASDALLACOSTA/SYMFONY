<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandesController
{
    /**
     * @Route("/commandes/recapitulatif")
     */
    function commande_recap()
    {
        return new Response("Récapitulatif des commandes");
    }

    /**
     * @Route("/commandes/{joker}")
     */
    function commande_en_cours_soldee($joker): Response
    {
        return new Response(sprintf("Liste des commandes : %s",$joker));
    }
}