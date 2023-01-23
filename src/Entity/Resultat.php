<?php

namespace App\Entity;

use App\Repository\ResultatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResultatRepository::class)]
class Resultat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Niveau::class, inversedBy: 'resultats')]
    #[ORM\JoinColumn(nullable: false)]
    private $niveau;

    #[ORM\ManyToOne(targetEntity: Semestre::class, inversedBy: 'resultats')]
    #[ORM\JoinColumn(nullable: false)]
    private $semestre;

    #[ORM\OneToMany(mappedBy: 'resultat', targetEntity: Note::class, cascade: ['persist'])]
    private $notes;

    #[ORM\ManyToOne(targetEntity: Examen::class, inversedBy: 'resultats')]
    #[ORM\JoinColumn(nullable: false)]
    private $examen;

    #[ORM\ManyToOne(targetEntity: UniteEnseignement::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $uniteEnseignement;

    #[ORM\ManyToOne(targetEntity: AnneeUniversitaire::class, inversedBy: 'resultats')]
    #[ORM\JoinColumn(nullable: false)]
    private $anneeUniversitaire;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
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

    /**
     * @return Collection<int, Note>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setResultat($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getResultat() === $this) {
                $note->setResultat(null);
            }
        }

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

    public function __toString()
    {
        return 'Resultat' . ' ' . $this->id;
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
}
