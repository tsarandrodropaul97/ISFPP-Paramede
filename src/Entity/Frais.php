<?php

namespace App\Entity;

use App\Repository\FraisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FraisRepository::class)]
class Frais
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'bigint')]
    private $ecolage;

    #[ORM\OneToOne(targetEntity: Niveau::class, cascade: ['persist', 'remove'])]
    private $niveau;

    #[ORM\Column(type: 'bigint')]
    private $droit;

    #[ORM\OneToMany(mappedBy: 'frais', targetEntity: Paiement::class)]
    private $paiements;

    #[ORM\OneToMany(mappedBy: 'frais', targetEntity: Etudiant::class)]
    private $etudiants;

    public function __construct()
    {
        $this->paiements = new ArrayCollection();
        $this->etudiants = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEcolage(): ?int
    {
        return $this->ecolage;
    }

    public function setEcolage(int $ecolage): self
    {
        $this->ecolage = $ecolage;

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

    public function getDroit(): ?string
    {
        return $this->droit;
    }

    public function setDroit(string $droit): self
    {
        $this->droit = $droit;

        return $this;
    }

    /**
     * @return Collection<int, Paiement>
     */
    public function getPaiements(): Collection
    {
        return $this->paiements;
    }

    public function addPaiement(Paiement $paiement): self
    {
        if (!$this->paiements->contains($paiement)) {
            $this->paiements[] = $paiement;
            $paiement->setFrais($this);
        }

        return $this;
    }

    public function removePaiement(Paiement $paiement): self
    {
        if ($this->paiements->removeElement($paiement)) {
            // set the owning side to null (unless already changed)
            if ($paiement->getFrais() === $this) {
                $paiement->setFrais(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Etudiant>
     */
    public function getEtudiants(): Collection
    {
        return $this->etudiants;
    }

    public function addEtudiant(Etudiant $etudiant): self
    {
        if (!$this->etudiants->contains($etudiant)) {
            $this->etudiants[] = $etudiant;
            $etudiant->setFrais($this);
        }

        return $this;
    }

    public function removeEtudiant(Etudiant $etudiant): self
    {
        if ($this->etudiants->removeElement($etudiant)) {
            // set the owning side to null (unless already changed)
            if ($etudiant->getFrais() === $this) {
                $etudiant->setFrais(null);
            }
        }

        return $this;
    }
}
