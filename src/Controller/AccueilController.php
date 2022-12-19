<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{

    function Accueil()
    {
        return $this->render('pages/accueil.html.twig', [
            'page_title' => 'Accueil',
        ]);
    }
}
