<?php

namespace App\Entity;

use App\Repository\NiveauRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NiveauRepository::class)]
class Niveau
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nom;

    #[ORM\Column(type: 'boolean')]
    private $statut;

    #[ORM\OneToMany(mappedBy: 'niveau', targetEntity: Etudiant::class)]
    private $etudiants;

    #[ORM\OneToMany(mappedBy: 'niveau', targetEntity: Paiement::class)]
    private $paiements;

    #[ORM\OneToMany(mappedBy: 'niveau', targetEntity: Semestre::class)]
    private $semestres;

    #[ORM\OneToMany(mappedBy: 'niveau', targetEntity: Resultat::class)]
    private $resultats;

    #[ORM\ManyToMany(targetEntity: UniteEnseignement::class, mappedBy: 'niveau')]
    private $uniteEnseignements;

    #[ORM\OneToMany(mappedBy: 'niveau', targetEntity: ArchiveResultat::class)]
    private $archiveResultats;

    public function __construct()
    {
        $this->etudiants = new ArrayCollection();
        $this->paiements = new ArrayCollection();
        $this->semestres = new ArrayCollection();
        $this->resultats = new ArrayCollection();
        $this->uniteEnseignements = new ArrayCollection();
        $this->archiveResultats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function __toString()
    {
        return $this->getNom();
    }

    public function isStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut): self
    {
        $this->statut = $statut;

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
            $etudiant->setNiveau($this);
        }

        return $this;
    }

    public function removeEtudiant(Etudiant $etudiant): self
    {
        if ($this->etudiants->removeElement($etudiant)) {
            // set the owning side to null (unless already changed)
            if ($etudiant->getNiveau() === $this) {
                $etudiant->setNiveau(null);
            }
        }

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
            $paiement->setNiveau($this);
        }

        return $this;
    }

    public function removePaiement(Paiement $paiement): self
    {
        if ($this->paiements->removeElement($paiement)) {
            // set the owning side to null (unless already changed)
            if ($paiement->getNiveau() === $this) {
                $paiement->setNiveau(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Semestre>
     */
    public function getSemestres(): Collection
    {
        return $this->semestres;
    }

    public function addSemestre(Semestre $semestre): self
    {
        if (!$this->semestres->contains($semestre)) {
            $this->semestres[] = $semestre;
            $semestre->setNiveau($this);
        }

        return $this;
    }

    public function removeSemestre(Semestre $semestre): self
    {
        if ($this->semestres->removeElement($semestre)) {
            // set the owning side to null (unless already changed)
            if ($semestre->getNiveau() === $this) {
                $semestre->setNiveau(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Resultat>
     */
    public function getResultats(): Collection
    {
        return $this->resultats;
    }

    public function addResultat(Resultat $resultat): self
    {
        if (!$this->resultats->contains($resultat)) {
            $this->resultats[] = $resultat;
            $resultat->setNiveau($this);
        }

        return $this;
    }

    public function removeResultat(Resultat $resultat): self
    {
        if ($this->resultats->removeElement($resultat)) {
            // set the owning side to null (unless already changed)
            if ($resultat->getNiveau() === $this) {
                $resultat->setNiveau(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UniteEnseignement>
     */
    public function getUniteEnseignements(): Collection
    {
        return $this->uniteEnseignements;
    }

    public function addUniteEnseignement(UniteEnseignement $uniteEnseignement): self
    {
        if (!$this->uniteEnseignements->contains($uniteEnseignement)) {
            $this->uniteEnseignements[] = $uniteEnseignement;
            $uniteEnseignement->addNiveau($this);
        }

        return $this;
    }

    public function removeUniteEnseignement(UniteEnseignement $uniteEnseignement): self
    {
        if ($this->uniteEnseignements->removeElement($uniteEnseignement)) {
            $uniteEnseignement->removeNiveau($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, ArchiveResultat>
     */
    public function getArchiveResultats(): Collection
    {
        return $this->archiveResultats;
    }

    public function addArchiveResultat(ArchiveResultat $archiveResultat): self
    {
        if (!$this->archiveResultats->contains($archiveResultat)) {
            $this->archiveResultats[] = $archiveResultat;
            $archiveResultat->setNiveau($this);
        }

        return $this;
    }

    public function removeArchiveResultat(ArchiveResultat $archiveResultat): self
    {
        if ($this->archiveResultats->removeElement($archiveResultat)) {
            // set the owning side to null (unless already changed)
            if ($archiveResultat->getNiveau() === $this) {
                $archiveResultat->setNiveau(null);
            }
        }

        return $this;
    }
}
