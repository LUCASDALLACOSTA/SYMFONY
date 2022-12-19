<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IntervenantController extends AbstractController
{

    public function index(){
        $entityManager = $this->getDoctrine()->getManager();

        $inter = new Intervenant();
        $inter->setNom("Leclerc");
        $inter->setPrenom("Gautier");
        $inter->setSpeciliteProfessionnelle("Docteur en informatique");
        $inter->setEmail("gautier.leclerc@gmail.com");
        $inter->setMdp("motdepasse");

        $entityManager->persist($inter);

        $entityManager->flush();

        return new Response("Création d'un intervenant avec l'id ".$inter->getId());

    }
}
