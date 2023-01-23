<?php

namespace App\Entity;

use App\Entity\ArchiveNote;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use App\Repository\UniteEnseignementRepository;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: UniteEnseignementRepository::class)]
class UniteEnseignement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nom;

    #[ORM\Column(type: 'integer')]
    private $credit;


    #[ORM\ManyToOne(targetEntity: Professeur::class, inversedBy: 'unitesEnseignements')]
    #[ORM\JoinColumn(nullable: false)]
    private $professeur;

    #[ORM\OneToMany(mappedBy: 'uniteEnseignement', targetEntity: Note::class)]
    private $notes;

    #[ORM\Column(type: 'boolean')]
    private $statut;


    #[ORM\ManyToMany(targetEntity: Semestre::class, inversedBy: 'uniteEnseignements')]
    private $semestre;

    #[ORM\ManyToMany(targetEntity: Niveau::class, inversedBy: 'uniteEnseignements')]
    private $niveau;

    #[ORM\OneToMany(mappedBy: 'uniteEnseignement', targetEntity: ArchiveResultat::class)]
    private $archiveResultats;

    #[ORM\OneToMany(mappedBy: 'uniteEnseignement', targetEntity: ArchiveNote::class)]
    private $archiveNotes;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
        $this->semestre = new ArrayCollection();
        $this->niveau = new ArrayCollection();
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

    public function getCredit(): ?int
    {
        return $this->credit;
    }

    public function setCredit(int $credit): self
    {
        $this->credit = $credit;

        return $this;
    }

    public function __toString()
    {
        return $this->nom;
    }

    public function getProfesseur(): ?Professeur
    {
        return $this->professeur;
    }

    public function setProfesseur(?Professeur $professeur): self
    {
        $this->professeur = $professeur;

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
            $note->setUniteEnseignement($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getUniteEnseignement() === $this) {
                $note->setUniteEnseignement(null);
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
     * @return Collection<int, Semestre>
     */
    public function getSemestre(): Collection
    {
        return $this->semestre;
    }

    public function addSemestre(Semestre $semestre): self
    {
        if (!$this->semestre->contains($semestre)) {
            $this->semestre[] = $semestre;
        }

        return $this;
    }

    public function removeSemestre(Semestre $semestre): self
    {
        $this->semestre->removeElement($semestre);

        return $this;
    }

    /**
     * @return Collection<int, Niveau>
     */
    public function getNiveau(): Collection
    {
        return $this->niveau;
    }

    public function addNiveau(Niveau $niveau): self
    {
        if (!$this->niveau->contains($niveau)) {
            $this->niveau[] = $niveau;
        }

        return $this;
    }

    public function removeNiveau(Niveau $niveau): self
    {
        $this->niveau->removeElement($niveau);

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
            $archiveResultat->setUniteEnseignement($this);
        }

        return $this;
    }

    public function removeArchiveResultat(ArchiveResultat $archiveResultat): self
    {
        if ($this->archiveResultats->removeElement($archiveResultat)) {
            // set the owning side to null (unless already changed)
            if ($archiveResultat->getUniteEnseignement() === $this) {
                $archiveResultat->setUniteEnseignement(null);
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
            $archiveNote->setUniteEnseignement($this);
        }

        return $this;
    }

    public function removeArchiveNote(ArchiveNote $archiveNote): self
    {
        if ($this->archiveNotes->removeElement($archiveNote)) {
            // set the owning side to null (unless already changed)
            if ($archiveNote->getUniteEnseignement() === $this) {
                $archiveNote->setUniteEnseignement(null);
            }
        }

        return $this;
    }
}
