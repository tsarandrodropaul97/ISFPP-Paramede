<?php

namespace App\Entity;

use App\Repository\ExamenRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExamenRepository::class)]
class Examen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nom;

    #[ORM\Column(type: 'date')]
    private $anneeExamen;

    #[ORM\OneToMany(mappedBy: 'examen', targetEntity: Resultat::class)]
    private $resultats;

    #[ORM\OneToMany(mappedBy: 'examen', targetEntity: Note::class)]
    private $notes;

    #[ORM\Column(type: 'string', length: 255)]
    private $type;

    #[ORM\ManyToOne(targetEntity: AnneeUniversitaire::class, inversedBy: 'examens')]
    #[ORM\JoinColumn(nullable: false)]
    private $anneeUniversitaire;

    #[ORM\OneToMany(mappedBy: 'examen', targetEntity: ArchiveResultat::class)]
    private $archiveResultats;

    #[ORM\OneToMany(mappedBy: 'examen', targetEntity: ArchiveNote::class)]
    private $archiveNotes;

    public function __construct()
    {
        $this->resultats = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->archiveResultats = new ArrayCollection();
        $this->archiveNotes = new ArrayCollection();
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

    public function getAnneeExamen(): ?\DateTimeInterface
    {
        return $this->anneeExamen;
    }

    public function setAnneeExamen(\DateTimeInterface $anneeExamen): self
    {
        $this->anneeExamen = $anneeExamen;

        return $this;
    }

    public function __toString()
    {
        return $this->nom;
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
            $resultat->setExamen($this);
        }

        return $this;
    }

    public function removeResultat(Resultat $resultat): self
    {
        if ($this->resultats->removeElement($resultat)) {
            // set the owning side to null (unless already changed)
            if ($resultat->getExamen() === $this) {
                $resultat->setExamen(null);
            }
        }

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
            $note->setExamen($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getExamen() === $this) {
                $note->setExamen(null);
            }
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
            $archiveResultat->setExamen($this);
        }

        return $this;
    }

    public function removeArchiveResultat(ArchiveResultat $archiveResultat): self
    {
        if ($this->archiveResultats->removeElement($archiveResultat)) {
            // set the owning side to null (unless already changed)
            if ($archiveResultat->getExamen() === $this) {
                $archiveResultat->setExamen(null);
            }
        }

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
            $archiveNote->setExamen($this);
        }

        return $this;
    }

    public function removeArchiveNote(ArchiveNote $archiveNote): self
    {
        if ($this->archiveNotes->removeElement($archiveNote)) {
            // set the owning side to null (unless already changed)
            if ($archiveNote->getExamen() === $this) {
                $archiveNote->setExamen(null);
            }
        }

        return $this;
    }
}
