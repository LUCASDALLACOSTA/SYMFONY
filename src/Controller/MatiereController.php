<?php

namespace App\Controller;

use App\Entity\Matiere;
use App\Form\MatiereFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\MatiereModifierType;
use Doctrine\ORM\EntityManagerInterface;

class MatiereController extends AbstractController
{
    /**
     * @Route("/matiere/create", name="matiere_create") //pour créer une matière
     */
    public function new(Request $request,ManagerRegistry $doctrine) //formulaire pour la création d'une matièere
    {
        $task = new Matiere();

        $form = $this->createForm(MatiereFormType::class,$task);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $task = $form->getData();

            $entityManager = $doctrine->getManager();
            $entityManager->persist($task);
            $entityManager->flush();
            return $this->redirectToRoute('matiere_liste');
        }

        return $this->render('matiere/new.html.twig',
            ['matiereForm' => $form->createView(),
            ]);
    }

    /**
     * @Route("/matiere/liste", name="matiere_liste") //pour la liste des matières
     */
    public function matiereListe(ManagerRegistry $doctrine)
    {
        $sousTitre = 'Liste des matières';
        $mats = $doctrine
            ->getRepository(Matiere::class)
            ->findAll();
        return $this->render('matiere/matiereListe.html.twig', [
            'matiere_liste' => $mats,
            'sous_titre' => $sousTitre,
    ]);
    }

    /**
     * @Route("/matiere/modifier/{id}", name="matiere_modifier") //pour modifier une matière
     */
    public function matiereModifier(Request $request, Matiere $matiere,ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(MatiereModifierType::class, $matiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getManager()->flush();

            return $this->redirectToRoute('matiere_liste');
        }

        return $this->render('matiere/matiereModifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/matiere/supprimer/{id}", name="matiere_supprimer") //pour supprimer une matière
     */
    public function supprimerMatiere(Matiere $matiere, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($matiere);
        $entityManager->flush();

        return $this->redirectToRoute('matiere_liste');
    }

}
