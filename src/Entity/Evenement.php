<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $debut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fin = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Intervenant $fk_intervenant = null;

    #[ORM\Column(nullable: true)]
    private ?bool $allday = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDebut(): ?\DateTimeInterface
    {
        return $this->debut;
    }

    public function setDebut(\DateTimeInterface $debut): self
    {
        $this->debut = $debut;

        return $this;
    }

    public function getFin(): ?\DateTimeInterface
    {
        return $this->fin;
    }

    public function setFin(\DateTimeInterface $fin): self
    {
        $this->fin = $fin;

        return $this;
    }

    public function getFkIntervenant(): ?Intervenant
    {
        return $this->fk_intervenant;
    }

    public function setFkIntervenant(?Intervenant $fk_intervenant): self
    {
        $this->fk_intervenant = $fk_intervenant;

        return $this;
    }

    public function isAllday(): ?bool
    {
        // Retourner false pour les cours
        if ($this->titre && strpos($this->titre, 'Cours de ') === 0) {
            return false;
        }

        // Sinon, retourner la valeur de la propriété "allday"
        return $this->allday;
    }

    public function getAllday(): ?bool
    {
        return $this->allday;
    }
    public function setAllday(?bool $allday): self
    {
        $this->allday = $allday;

        return $this;
    }

}
