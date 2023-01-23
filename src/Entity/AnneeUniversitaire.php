<?php

namespace App\Entity;

use App\Repository\AnneeUniversitaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnneeUniversitaireRepository::class)]
class AnneeUniversitaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'date')]
    private $dateDeRentree;

    #[ORM\Column(type: 'date')]
    private $dateDeFin;

    #[ORM\Column(type: 'string', length: 255)]
    private $nom;

    #[ORM\OneToMany(mappedBy: 'anneeUniversitaire', targetEntity: Resultat::class)]
    private $resultats;

    #[ORM\OneToMany(mappedBy: 'anneeUniversitaire', targetEntity: Examen::class)]
    private $examens;

    #[ORM\OneToMany(mappedBy: 'anneeUniversitaire', targetEntity: ArchiveResultat::class)]
    private $archiveResultats;

    public function __construct()
    {
        $this->resultats = new ArrayCollection();
        $this->examens = new ArrayCollection();
        $this->archiveResultats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDeRentree(): ?\DateTimeInterface
    {
        return $this->dateDeRentree;
    }

    public function setDateDeRentree(\DateTimeInterface $dateDeRentree): self
    {
        $this->dateDeRentree = $dateDeRentree;

        return $this;
    }

    public function getDateDeFin(): ?\DateTimeInterface
    {
        return $this->dateDeFin;
    }

    public function setDateDeFin(\DateTimeInterface $dateDeFin): self
    {
        $this->dateDeFin = $dateDeFin;

        return $this;
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
            $resultat->setAnneeUniversitaire($this);
        }

        return $this;
    }

    public function removeResultat(Resultat $resultat): self
    {
        if ($this->resultats->removeElement($resultat)) {
            // set the owning side to null (unless already changed)
            if ($resultat->getAnneeUniversitaire() === $this) {
                $resultat->setAnneeUniversitaire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Examen>
     */
    public function getExamens(): Collection
    {
        return $this->examens;
    }

    public function addExamen(Examen $examen): self
    {
        if (!$this->examens->contains($examen)) {
            $this->examens[] = $examen;
            $examen->setAnneeUniversitaire($this);
        }

        return $this;
    }

    public function removeExamen(Examen $examen): self
    {
        if ($this->examens->removeElement($examen)) {
            // set the owning side to null (unless already changed)
            if ($examen->getAnneeUniversitaire() === $this) {
                $examen->setAnneeUniversitaire(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->nom;
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
            $archiveResultat->setAnneeUniversitaire($this);
        }

        return $this;
    }

    public function removeArchiveResultat(ArchiveResultat $archiveResultat): self
    {
        if ($this->archiveResultats->removeElement($archiveResultat)) {
            // set the owning side to null (unless already changed)
            if ($archiveResultat->getAnneeUniversitaire() === $this) {
                $archiveResultat->setAnneeUniversitaire(null);
            }
        }

        return $this;
    }
}
