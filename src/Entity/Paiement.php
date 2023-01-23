<?php

namespace App\Entity;

use App\Repository\PaiementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementRepository::class)]
class Paiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Etudiant::class, inversedBy: 'paiements')]
    private $etudiant;

    #[ORM\ManyToOne(targetEntity: Frais::class, inversedBy: 'paiements')]
    private $frais;

    #[ORM\Column(type: 'datetime')]
    private $datePaiment;

    #[ORM\ManyToOne(targetEntity: Niveau::class, inversedBy: 'paiements')]
    private $niveau;

    #[ORM\Column(type: 'date')]
    private $payer;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtudiant(): ?Etudiant
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Etudiant $etudiant): self
    {
        $this->etudiant = $etudiant;

        return $this;
    }

    public function getFrais(): ?Frais
    {
        return $this->frais;
    }

    public function setFrais(?Frais $frais): self
    {
        $this->frais = $frais;

        return $this;
    }

    public function getDatePaiment(): ?\DateTimeInterface
    {
        return $this->datePaiment;
    }

    public function setDatePaiment(\DateTimeInterface $datePaiment): self
    {
        $this->datePaiment = $datePaiment;

        return $this;
    }

    public function getNiveau(): ?Niveau
    {
        return $this->niveau;
    }

    public function setNiveau(?Niveau $niveau): self
    {
        $this->niveau = $niveau;

        return $this;
    }

    public function getPayer(): ?\DateTimeInterface
    {
        return $this->payer;
    }

    public function setPayer(\DateTimeInterface $payer): self
    {
        $this->payer = $payer;

        return $this;
    }
}
