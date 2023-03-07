<?php

namespace App\Controller;

use App\Entity\Intervenant;
use App\Entity\Matiere;
use App\Form\IntervenantModifierType;
use App\Form\IntervenantFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class IntervenantController extends AbstractController
{
    /**
     * @Route("/intervenant")
     **/
    public function index(ManagerRegistry $doctrine) //création en dur dans la bdd
    {
        //recuperation de l'objet EntityManager via $this->getDoctrine()
        $entityManager = $doctrine->getManager();

        $inter = new Intervenant();
        //$inter->setNom('Leclerc');
        //$inter->setPrenom("Gautier");
        //$inter->setSpecialiteprofessionnelle("Docteur en informatique");
        //$inter->setEmail('gautier.leclerc@gmail.com');
        //$inter->setNom('Dubois');
        //$inter->setPrenom("Suzanne");
        //$inter->setSpecialiteprofessionnelle("Ingénieur système");
        //$inter->setEmail('suzanne.dubois@gmail.com');

        //informer Doctrine que l'on veut persister ces données
        $entityManager->persist($inter);

        //execution de la requete
        $entityManager->flush();

        return new Response("Création d'un intervenant avec l'id".$inter->getId());
    }

    /**
     * @Route("/intervenant/liste", name="intervenant_liste") //pour la liste des intervenants
     * @Security("is_granted('ROLE_ADMIN')", message="Accès refusé")
     **/
    public function show(ManagerRegistry $doctrine) //liste des intervenants
    {
        $intervenants = $doctrine->getRepository(Intervenant::class)->findAll();

        return $this->render('intervenant/liste.html.twig',
            array('intervenants' => $intervenants),
        );
    }

    /**
     * @Route("/intervenant/create", name="intervenant_create") //pour créer un intervenant
     * @Security("is_granted('ROLE_ADMIN')", message="Accès refusé")
     */
    public function new(Request $request,ManagerRegistry $doctrine) //formulaire pour la création d'un intervenant
    {
        $task = new Intervenant();

        $form = $this->createForm(IntervenantFormType::class,$task);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $task = $form->getData();

            $entityManager = $doctrine->getManager();
            $entityManager->persist($task);
            $entityManager->flush();
            return $this->redirectToRoute('newIntervenant_success');
        }

        return $this->render('intervenant/new.html.twig',
            ['intervenantForm' => $form->createView(),
            ]);
    }

    /**
     * @Route("/intervenant/dashboard", name="intervenant_dashboard") //dashboard d'un intervenant
     *
     */
    public function intervenantsDashboard()
    {
        return $this->render('intervenant/intervenant_dashboard.html.twig',[

        ]);
    }

    /**
     * @Route("/intervenant/matieres/liste", name="intervenant_matieres_liste") //pour la liste des matières selon l'intervenant
     *
     */
    public function intervenantMatieresListe(ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        $intervenant = $doctrine->getRepository(Intervenant::class)->findOneBy(['email' => $user->getEmail()]);
        $matieres = $doctrine->getRepository(Matiere::class)->findBy(['fk_intervenant' => $intervenant]);

        return $this->render('intervenant/intervenant_matieres_liste.html.twig', [
            'matieres' => $matieres,
        ]);
    }

    /**
     * @Route("/intervenant/modifier/{id}", name="intervenant_modifier") //pour modifier un intervenant
     * @Security("is_granted('ROLE_ADMIN')", message="Accès refusé")
     */
    public function modifierIntervenant(Request $request, Intervenant $intervenant, ManagerRegistry $doctrine)
    {
        $form = $this->createForm(IntervenantModifierType::class, $intervenant);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('intervenant_liste');
        }

        return $this->render('intervenant/intervenantModifier.html.twig', [
            'intervenantForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/intervenant/supprimer/{id}", name="intervenant_supprimer") //pour supprimer un intervenant
     * @Security("is_granted('ROLE_ADMIN')", message="Accès refusé")
     */
    public function supprimerIntervenant(Request $request, Intervenant $intervenant, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($intervenant);
        $entityManager->flush();

        return $this->redirectToRoute('intervenant_liste');
    }

}