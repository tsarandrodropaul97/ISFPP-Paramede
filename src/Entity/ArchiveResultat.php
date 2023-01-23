<?php

namespace App\Entity;

use App\Repository\ArchiveResultatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArchiveResultatRepository::class)]
class ArchiveResultat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Niveau::class, inversedBy: 'archiveResultats')]
    #[ORM\JoinColumn(nullable: false)]
    private $niveau;

    #[ORM\ManyToOne(targetEntity: Semestre::class, inversedBy: 'archiveResultats')]
    #[ORM\JoinColumn(nullable: false)]
    private $semestre;

    #[ORM\ManyToOne(targetEntity: Examen::class, inversedBy: 'archiveResultats')]
    #[ORM\JoinColumn(nullable: false)]
    private $examen;

    #[ORM\ManyToOne(targetEntity: UniteEnseignement::class, inversedBy: 'archiveResultats')]
    #[ORM\JoinColumn(nullable: false)]
    private $uniteEnseignement;

    #[ORM\ManyToOne(targetEntity: AnneeUniversitaire::class, inversedBy: 'archiveResultats')]
    #[ORM\JoinColumn(nullable: false)]
    private $anneeUniversitaire;

    #[ORM\OneToMany(mappedBy: 'resultat', targetEntity: ArchiveNote::class)]
    private $archiveNotes;

    public function __construct()
    {
        $this->archiveNotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSemestre(): ?Semestre
    {
        return $this->semestre;
    }

    public function setSemestre(?Semestre $semestre): self
    {
        $this->semestre = $semestre;

        return $this;
    }

    public function getExamen(): ?Examen
    {
        return $this->examen;
    }

    public function setExamen(?Examen $examen): self
    {
        $this->examen = $examen;

        return $this;
    }

    public function getUniteEnseignement(): ?UniteEnseignement
    {
        return $this->uniteEnseignement;
    }

    public function setUniteEnseignement(?UniteEnseignement $uniteEnseignement): self
    {
        $this->uniteEnseignement = $uniteEnseignement;

        return $this;
    }

    public function getAnneeUniversitaire(): ?AnneeUniversitaire
    {
        return $this->anneeUniversitaire;
    }

    public function setAnneeUniversitaire(?AnneeUniversitaire $anneeUniversitaire): self
    {
        $this->anneeUniversitaire = $anneeUniversitaire;

        return $this;
    }

    /**
     * @return Collection<int, ArchiveNote>
     */
    public function getArchiveNotes(): Collection
    {
        return $this->archiveNotes;
    }

    public function addArchiveNote(ArchiveNote $archiveNote): self
    {
        if (!$this->archiveNotes->contains($archiveNote)) {
            $this->archiveNotes[] = $archiveNote;
            $archiveNote->setResultat($this);
        }

        return $this;
    }

    public function removeArchiveNote(ArchiveNote $archiveNote): self
    {
        if ($this->archiveNotes->removeElement($archiveNote)) {
            // set the owning side to null (unless already changed)
            if ($archiveNote->getResultat() === $this) {
                $archiveNote->setResultat(null);
            }
        }

        return $this;
    }
}
