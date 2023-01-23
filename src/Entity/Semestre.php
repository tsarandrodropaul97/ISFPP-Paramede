<?php

namespace App\Entity;

use App\Entity\ArchiveResultat;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SemestreRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: SemestreRepository::class)]
class Semestre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nom;

    #[ORM\ManyToOne(targetEntity: Niveau::class, inversedBy: 'semestres')]
    #[ORM\JoinColumn(nullable: false)]
    private $niveau;

    #[ORM\OneToMany(mappedBy: 'semestre', targetEntity: Resultat::class)]
    private $resultats;

    #[ORM\Column(type: 'boolean')]
    private $statut;

    #[ORM\ManyToMany(targetEntity: UniteEnseignement::class, mappedBy: 'semestre')]
    private $uniteEnseignements;

    #[ORM\Column(type: 'string', length: 255)]
    private $type;

    #[ORM\OneToMany(mappedBy: 'semestre', targetEntity: ArchiveResultat::class)]
    private $archiveResultats;

    public function __construct()
    {
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
        return $this->nom;
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
            $resultat->setSemestre($this);
        }

        return $this;
    }

    public function removeResultat(Resultat $resultat): self
    {
        if ($this->resultats->removeElement($resultat)) {
            // set the owning side to null (unless already changed)
            if ($resultat->getSemestre() === $this) {
                $resultat->setSemestre(null);
            }
        }

        return $this;
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
            $uniteEnseignement->addSemestre($this);
        }

        return $this;
    }

    public function removeUniteEnseignement(UniteEnseignement $uniteEnseignement): self
    {
        if ($this->uniteEnseignements->removeElement($uniteEnseignement)) {
            $uniteEnseignement->removeSemestre($this);
        }

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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
            $archiveResultat->setSemestre($this);
        }

        return $this;
    }

    public function removeArchiveResultat(ArchiveResultat $archiveResultat): self
    {
        if ($this->archiveResultats->removeElement($archiveResultat)) {
            // set the owning side to null (unless already changed)
            if ($archiveResultat->getSemestre() === $this) {
                $archiveResultat->setSemestre(null);
            }
        }

        return $this;
    }
}
