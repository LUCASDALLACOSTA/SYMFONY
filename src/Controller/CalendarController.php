<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Evenement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class CalendarController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/calendar", name="calendar")
     */
    public function index()
    {
        return $this->render('/calendar/calendar.html.twig', [
        ]);
    }
    /**
     * @Route("/calendar/afficher", name="calendar_afficher")
     */
    public function afficher()
    {
        $evenements = $this->entityManager->getRepository(Evenement::class)->findAll();
        $data = [];

        foreach ($evenements as $evenement) {
            if (stripos($evenement->getTitre(), 'Disponibilité') === false) {
                if ($evenement->isAllday()) { // Vérifier si l'événement doit être affiché dans la partie "allday"
                    $data[] = [
                        'id' => $evenement->getId(),
                        'title' => $evenement->getTitre(),
                        'start' => $evenement->getDebut()->format('Y-m-d'),
                        'end' => $evenement->getFin()->format('Y-m-d'),
                        'allDay' => true, // Spécifier que l'événement doit être affiché dans la partie "allday"
                    ];
                } else { // Sinon, afficher l'événement avec les heures
                    $data[] = [
                        'id' => $evenement->getId(),
                        'title' => $evenement->getTitre(),
                        'start' => $evenement->getDebut()->format(\DateTime::RFC3339),
                        'end' => $evenement->getFin()->format(\DateTime::RFC3339),
                    ];
                }
            }
        }

        $data = json_encode($data);

        $response = new Response();
        $response->setContent($data);
        return $response;
    }

}
