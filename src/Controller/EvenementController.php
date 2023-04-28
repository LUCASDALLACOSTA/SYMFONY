<?php

namespace App\Controller;

use App\Entity\Event;
use App\Controller\User;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Intervenant;
use App\Form\JourFerieType;
use App\Form\SemaineEntrepriseType;
use App\Entity\Evenement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\IntervenantRepository;
use App\Form\DisponibiliteType;
use App\Form\CoursType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

class EvenementController extends AbstractController
{

    /**
     * @Route("/semaine_entreprise/liste", name="semaine_entreprise")
     */
    public function listeSemainesEntreprise(Request $request, ManagerRegistry $doctrine)
    {
        $evenements = $doctrine->getRepository(Evenement::class)->findBy(['titre' => "Semaine d'entreprise"]);

        return $this->render('evenement/semaine_entreprise.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    /**
     * @Route("/semaine_entreprise/nouvelle", name="nouvelle_semaine_entreprise")    //permet de créer une nouvelle semaine d'entreprise
     */
    public function nouvelleSemaineEntreprise(Request $request, ManagerRegistry $doctrine): Response
    {
        $evenement = new Evenement();
        $evenement->setTitre('Semaine d\'entreprise');

        $form = $this->createForm(SemaineEntrepriseType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer la période sélectionnée
            $debut = $evenement->getDebut();
            $fin = $evenement->getFin();

            // Vérifier si une semaine d'entreprise existe déjà pour cette période
            $repository = $doctrine->getRepository(Evenement::class);
            $evenements = $repository->createQueryBuilder('e')
                ->where('e.titre = :titre')
                ->andWhere('e.debut <= :fin')
                ->andWhere('e.fin >= :debut')
                ->setParameter('titre', 'Semaine d\'entreprise')
                ->setParameter('debut', $debut)
                ->setParameter('fin', $fin)
                ->getQuery()
                ->getResult();

            if (count($evenements) > 0) {
                $this->addFlash('error', 'Une semaine d\'entreprise existe déjà pour cette période.');
                return $this->render('evenement/nouvelle_semaine_entreprise.html.twig', [
                    'form' => $form->createView(),
                ]);
            } else {
                // Mettre l'heure de début et de fin à 00:00
                $debut->setTime(0, 0);
                $evenement->setDebut($debut);

                $fin->modify('+1 day');
                $fin->setTime(0, 0);
                $evenement->setFin($fin);

                $entityManager = $doctrine->getManager();
                $entityManager->persist($evenement);
                $entityManager->flush();

                $this->addFlash('success', 'La semaine d\'entreprise a été créée avec succès.');

                return $this->redirectToRoute('semaine_entreprise');
            }
        }

        return $this->render('evenement/nouvelle_semaine_entreprise.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/semaine_entreprise/supprimer/{id}", name="semaine_entreprise_supprimer")
     */
    public function supprimerSemaineEntreprise(Evenement $evenement, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($evenement);
        $entityManager->flush();

        return $this->redirectToRoute('semaine_entreprise');
    }

    /**
     * @Route("/jour_ferie/liste", name="jour_ferie")
     */
    public function listeJoursFeries(Request $request, ManagerRegistry $doctrine)
    {
        $evenements = $doctrine->getRepository(Evenement::class)->findBy(['titre' => "Jour férié"]);

        return $this->render('evenement/jour_ferie.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    /**
     * @Route("/jour_ferie/nouveau", name="nouveau_jour_ferie")  //permet de créer un nouveau jour férié
     */
    public function nouveauJourFerie(Request $request, ManagerRegistry $doctrine): Response
    {
        $evenement = new Evenement();
        $evenement->setTitre('Jour férié');

        $form = $this->createForm(JourFerieType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer la période sélectionnée
            $debut = $evenement->getDebut();

            // Vérifier si un jour ferié existe déjà pour cette période
            $repository = $doctrine->getRepository(Evenement::class);
            $evenements = $repository->createQueryBuilder('e')
                ->where('e.titre = :titre')
                ->andWhere('e.debut = :debut')
                ->setParameter('titre', 'jour férié')
                ->setParameter('debut', $debut)
                ->getQuery()
                ->getResult();

            if (count($evenements) > 0) {
                $this->addFlash('error', 'Ce jour férié est déjà renseigné');
            } else {
                $debut = $evenement->getDebut();
                $debut->setTime(0, 0);
                $evenement->setDebut($debut);

                $fin = clone $debut;
                $fin->setTime(23, 59);
                $evenement->setFin($fin);

                $entityManager = $doctrine->getManager();
                $entityManager->persist($evenement);
                $entityManager->flush();

                $this->addFlash('success', 'Le jour férié a été créé avec succès.');

                return $this->redirectToRoute('jour_ferie');
            }
        }

        return $this->render('evenement/nouveau_jour_ferie.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/jour_ferie/supprimer/{id}", name="jour_ferie_supprimer")
     */
    public function supprimerJourFerie(Evenement $evenement, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($evenement);
        $entityManager->flush();

        return $this->redirectToRoute('jour_ferie');
    }

    /**
     * @Route("/disponibilite/liste", name="disponibilite")
     */
    public function listeDisponibilites(Request $request, ManagerRegistry $doctrine)
    {
        $evenements = $doctrine->getRepository(Evenement::class)->findBy(['titre' => "Disponibilité de "]);

        return $this->render('evenement/disponibilite.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    /**
     * @Route("/disponibilite/nouvelle", name="nouvelle_disponibilite")
     */
    public function nouvelleDisponibilite(Request $request, ManagerRegistry $doctrine, UserInterface $user): Response
    {
        $evenement = new Evenement();
        $evenement->setTitre('Disponibilité de ');

        $form = $this->createForm(DisponibiliteType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $debut = $evenement->getDebut();
            $evenement->setDebut($debut);

            $fin = $evenement->getFin();
            $evenement->setDebut($fin);

            $intervenantRepository = $doctrine->getRepository(Intervenant::class);
            $intervenant = $intervenantRepository->findOneBy(['email' => $user->getEmail()]);

            if ($intervenant !== null) {
                $evenement->setFkIntervenant($intervenant);
            }

            $entityManager = $doctrine->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();

            $this->addFlash('success', 'La disponibilité a été créée avec succès.');

            return $this->redirectToRoute('disponibilite');
        }

        return $this->render('evenement/nouvelle_disponibilite.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/disponibilite/supprimer/{id}", name="disponibilite_supprimer")
     */
    public function supprimerDisponibilite(Evenement $evenement, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($evenement);
        $entityManager->flush();

        return $this->redirectToRoute('disponibilite');
    }

    /**
     * @Route("/cours/liste", name="cours")
     */
    public function listeCours(Request $request, ManagerRegistry $registry)
    {
        $em = $registry->getManager();
        $evenements = $em->getRepository(Evenement::class)
            ->createQueryBuilder('e')
            ->where('e.titre LIKE :titre')
            ->setParameter('titre', 'Cours%')
            ->getQuery()
            ->getResult();

        return $this->render('evenement/cours.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    /**
     * @Route("/cours/nouveau", name="nouveau_cours")
     */
    public function nouveauCours(Request $request, ManagerRegistry $doctrine): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(CoursType::class, $evenement);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $evenement = $form->getData();
            $evenement->setTitre("Cours de " . $form->get('titre')->getData());
            //$evenement->setFkIntervenant($form->get('fk_intervenant')->getData()->getId());
            $evenement->setAllDay(false);

            // Récupérer la période sélectionnée
            $debut = $evenement->getDebut();
            $fin = $evenement->getFin();

            $repository = $doctrine->getRepository(Evenement::class);
            $evenements = $repository->createQueryBuilder('e')
                ->where('e.titre IN (:titre) OR e.titre LIKE :cours')
                ->andWhere('e.debut <= :fin')
                ->andWhere('e.fin >= :debut')
                ->setParameter('titre', ['Semaine d\'entreprise', 'Jour férié'])
                ->setParameter('cours', 'Cours de%')
                ->setParameter('debut', $debut)
                ->setParameter('fin', $fin)
                ->getQuery()
                ->getResult();

            if (count($evenements) > 0) {
                $this->addFlash('error', 'Un événement Semaine d\'entreprise ou Jour férié existe déjà pour cette période.');
                return $this->render('evenement/nouveau_cours.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            $entityManager = $doctrine->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();

            $this->addFlash('success', 'Le cours a été créé avec succès.');

            return $this->redirectToRoute('cours');
        }

        return $this->render('evenement/nouveau_cours.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/cours/supprimer/{id}", name="cours_supprimer")
     */
    public function supprimerCours(Evenement $evenement, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($evenement);
        $entityManager->flush();

        return $this->redirectToRoute('cours');
    }


}